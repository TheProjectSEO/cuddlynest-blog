# 🚀 CuddlyNest Blog - Deployment Guide for Infrastructure Teams

## 📋 Quick Start

### Prerequisites
- Docker 20.10+ and Docker Compose 2.0+
- Git access to this repository
- Environment variables (see `.env.example`)

### One-Command Deployment
```bash
git clone https://github.com/TheProjectSEO/cuddlynest-blog.git
cd cuddlynest-blog
cp .env.example .env.local  # Edit with your values
docker-compose up -d
```

## 🐳 Docker Configuration

### Production Dockerfile Features
- **Multi-stage build**: Optimized for production
- **Security hardened**: Non-root user, minimal attack surface  
- **Alpine Linux**: Small, secure base image
- **Health checks**: Built-in container monitoring
- **Standalone output**: Self-contained Next.js deployment

### Build Arguments
```bash
docker build \
  --build-arg BUILD_DATE=$(date -u +'%Y-%m-%dT%H:%M:%SZ') \
  --build-arg VERSION=1.0.0 \
  --build-arg VCS_REF=$(git rev-parse --short HEAD) \
  -t cuddlynest-blog:latest .
```

## 🔧 Environment Variables

### Required Variables
```env
# Database (Supabase)
NEXT_PUBLIC_SUPABASE_URL=https://xxx.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJxxx
SUPABASE_SERVICE_ROLE_KEY=eyJxxx

# AI Services  
GOOGLE_API_KEY=AIzaxxx         # Image generation
MISTRAL_API_KEY=xxx            # Translations

# Application
NEXT_PUBLIC_SITE_URL=https://your-domain.com
```

### Optional Variables
```env
GOOGLE_TRANSLATE_API_KEY=xxx   # Fallback translation
NODE_ENV=production
PORT=3000
NEXT_TELEMETRY_DISABLED=1
```

## 🏗 Deployment Options

### Option 1: Docker Compose (Recommended)
```bash
docker-compose up -d
```

### Option 2: Docker Run
```bash
docker run -d \
  --name cuddlynest-blog \
  -p 3000:3000 \
  --env-file .env.local \
  --restart unless-stopped \
  cuddlynest-blog:latest
```

### Option 3: Kubernetes
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: cuddlynest-blog
spec:
  replicas: 3
  selector:
    matchLabels:
      app: cuddlynest-blog
  template:
    spec:
      containers:
      - name: cuddlynest-blog
        image: cuddlynest-blog:latest
        ports:
        - containerPort: 3000
        env:
        - name: NEXT_PUBLIC_SUPABASE_URL
          valueFrom:
            secretKeyRef:
              name: cuddlynest-secrets
              key: supabase-url
```

## 🔍 Health Monitoring

### Health Check Endpoint
- **URL**: `http://localhost:3000/api/health`
- **Method**: GET
- **Expected Response**: `{"status": "healthy", "timestamp": "..."}`

### Container Health Check
```bash
# Check container health status
docker inspect --format='{{.State.Health.Status}}' cuddlynest-blog

# View health check logs
docker inspect --format='{{range .State.Health.Log}}{{.Output}}{{end}}' cuddlynest-blog
```

## 📊 Resource Requirements

### Minimum Resources
- **CPU**: 0.5 vCPU
- **Memory**: 512MB RAM
- **Storage**: 2GB (includes images, cache)
- **Network**: 1Gbps recommended

### Recommended Production
- **CPU**: 1-2 vCPU
- **Memory**: 1-2GB RAM  
- **Storage**: 10GB SSD
- **Load Balancer**: For multiple instances

## 🔐 Security Considerations

### Container Security
- Runs as non-root user (UID 1001)
- Minimal Alpine Linux base image
- No unnecessary packages or tools
- Security headers configured in Next.js

### Environment Security
- Use Docker secrets for sensitive data
- Enable firewall rules (only port 3000)
- Regular security updates via base image updates
- Environment variables validation on startup

## 📈 Scaling & Performance

### Horizontal Scaling
```bash
# Scale with Docker Compose
docker-compose up -d --scale cuddlynest-blog=3

# With load balancer
# Configure nginx/traefik upstream to multiple containers
```

### Performance Optimization
- **CDN**: Configure for `/public/` assets
- **Database**: Optimize Supabase connection pooling
- **Caching**: Redis for session/translation cache (optional)
- **Monitoring**: Prometheus + Grafana recommended

## 🚨 Troubleshooting

### Common Issues

#### Container Won't Start
```bash
# Check logs
docker logs cuddlynest-blog

# Common causes:
# - Missing environment variables
# - Database connection issues
# - Port conflicts
```

#### Health Check Fails
```bash
# Test health endpoint manually
curl http://localhost:3000/api/health

# Check container status
docker ps -a
docker inspect cuddlynest-blog
```

#### Build Failures
```bash
# Clean build (no cache)
docker build --no-cache -t cuddlynest-blog .

# Check .dockerignore is present
# Verify all files are committed to git
```

### Performance Issues
```bash
# Check resource usage
docker stats cuddlynest-blog

# Monitor application logs
docker logs -f cuddlynest-blog

# Database connection issues
# Verify Supabase credentials and network access
```

## 💾 Backup & Recovery

### Database Backup
- Supabase handles automated backups
- Configure point-in-time recovery
- Regular manual exports recommended

### Application Backup
```bash
# Backup volumes (if using persistent storage)
docker run --rm -v cuddlynest_data:/data alpine tar czf - -C /data . > backup.tar.gz

# Restore from backup
docker run --rm -v cuddlynest_data:/data alpine sh -c "cd /data && tar xzf -" < backup.tar.gz
```

## 📞 Support & Contacts

### Emergency Contacts
- **Development Team**: [team@cuddlynest.com]
- **Repository**: https://github.com/TheProjectSEO/cuddlynest-blog
- **Documentation**: See README.md for detailed technical docs

### Monitoring Alerts
Set up alerts for:
- Container health check failures
- High memory/CPU usage (>80%)
- Database connection failures
- API service quota exceeded (Mistral/Google AI)

### Cost Monitoring
- Monitor AI service usage monthly
- Set spending alerts in Google Cloud/Mistral dashboards
- Expected costs: $35-220/month (see README.md)

---

**Generated with Claude Code** | Last Updated: $(date)