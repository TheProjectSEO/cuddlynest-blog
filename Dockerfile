# CuddlyNest Travel Blog - Production Dockerfile
# Multi-stage build for optimized production deployment

# ==========================================
# Stage 1: Dependencies & Build
# ==========================================
FROM node:18-alpine AS deps

# Install dependencies needed for node-gyp and native modules
RUN apk add --no-cache libc6-compat

# Set working directory
WORKDIR /app

# Copy package files for dependency installation
COPY package.json package-lock.json* ./

# Install dependencies with production optimizations
# Use npm ci if package-lock.json exists, otherwise npm install
RUN if [ -f package-lock.json ]; then \
        echo "📦 Installing with npm ci (using lock file)" && \
        npm ci --omit=dev --ignore-scripts; \
    else \
        echo "⚠️  No lock file found, using npm install (less reliable)" && \
        npm install --omit=dev --ignore-scripts; \
    fi && \
    npm cache clean --force

# ==========================================
# Stage 2: Build Application
# ==========================================
FROM node:18-alpine AS builder

WORKDIR /app

# Copy dependencies from deps stage
COPY --from=deps /app/node_modules ./node_modules

# Copy source code
COPY . .

# Install all dependencies (including dev dependencies for build)
# Use npm ci if package-lock.json exists, otherwise npm install
RUN if [ -f package-lock.json ]; then \
        echo "📦 Building with npm ci (using lock file)" && \
        npm ci --ignore-scripts; \
    else \
        echo "⚠️  Building with npm install (no lock file)" && \
        npm install --ignore-scripts; \
    fi

# Set environment variables for build
ENV NEXT_TELEMETRY_DISABLED 1
ENV NODE_ENV production

# Build the Next.js application
RUN npm run build

# ==========================================
# Stage 3: Production Runtime
# ==========================================
FROM node:18-alpine AS runner

# Install security updates and dumb-init for proper signal handling
RUN apk add --no-cache dumb-init && \
    apk upgrade --no-cache

WORKDIR /app

# Create non-root user for security
RUN addgroup --system --gid 1001 nodejs && \
    adduser --system --uid 1001 nextjs

# Set production environment
ENV NODE_ENV production
ENV NEXT_TELEMETRY_DISABLED 1
ENV PORT 3000
ENV HOSTNAME "0.0.0.0"

# Copy public assets
COPY --from=builder /app/public ./public

# Create .next directory with correct permissions
RUN mkdir .next && chown nextjs:nodejs .next

# Copy built application with correct ownership
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

# Copy necessary configuration files
COPY --from=builder --chown=nextjs:nodejs /app/next.config.mjs ./
COPY --from=builder --chown=nextjs:nodejs /app/redirects.mjs ./

# Switch to non-root user
USER nextjs

# Expose the application port
EXPOSE 3000

# Health check for container monitoring
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD node healthcheck.js || exit 1

# Use dumb-init for proper signal handling and start the application
ENTRYPOINT ["dumb-init", "--"]
CMD ["node", "server.js"]

# ==========================================
# Build Arguments and Labels
# ==========================================
ARG BUILD_DATE
ARG VERSION
ARG VCS_REF

LABEL org.label-schema.build-date=$BUILD_DATE \
      org.label-schema.name="cuddlynest-blog" \
      org.label-schema.description="CuddlyNest Travel Blog - Next.js Application" \
      org.label-schema.version=$VERSION \
      org.label-schema.vcs-ref=$VCS_REF \
      org.label-schema.vcs-url="https://github.com/your-org/cuddly-nest-blog" \
      org.label-schema.schema-version="1.0" \
      maintainer="CuddlyNest Development Team"