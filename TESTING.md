# Testing Guide - Cloud Platform Services

Ky dokument përshkruan si të testohen të gjitha serviset e platformës cloud.

## Portet e Serviseve

- **auth-service**: Port 8000
- **resource-service**: Port 8001
- **monitoring-service**: Port 8002
- **autoscaler-service**: Port 8003
- **api-gateway**: Port 8004

## Si të Startoni Serviset

### 1. Startoni të gjitha serviset në terminala të ndryshme:

```bash
# Terminal 1 - Auth Service
cd auth-service
php artisan serve --port=8000

# Terminal 2 - Resource Service
cd resource-service
php artisan serve --port=8001

# Terminal 3 - Monitoring Service
cd monitoring-service
php artisan serve --port=8002

# Terminal 4 - Autoscaler Service
cd autoscaler-service
php artisan serve --port=8003

# Terminal 5 - API Gateway
cd api-gateway
php artisan serve --port=8004
```

## Testimi i Autoscaler Service

### 1. Test Health Check

```bash
curl http://localhost:8003/api/status
```

Përgjigja e pritur:
```json
{
  "status": "operational",
  "enabled": true,
  "current_metrics": {...},
  "auto_scaled_resources_count": 0,
  "total_resources": 0,
  "thresholds": {...},
  "scaling_limits": {...}
}
```

### 2. Test Analyze Endpoint

```bash
curl http://localhost:8003/api/analyze
```

Kjo kthen analizën aktuale të metrikave dhe vendimin për scaling.

### 3. Test Manual Scale Up (kërkon authentication)

```bash
# Së pari merrni token nga auth-service
TOKEN="your-auth-token-here"

curl -X POST http://localhost:8003/api/scale-up \
  -H "Authorization: Bearer $TOKEN"
```

### 4. Test Manual Scale Down (kërkon authentication)

```bash
curl -X POST http://localhost:8003/api/scale-down \
  -H "Authorization: Bearer $TOKEN"
```

### 5. Test History (kërkon authentication)

```bash
curl http://localhost:8003/api/history \
  -H "Authorization: Bearer $TOKEN"
```

## Testimi i API Gateway

### 1. Test Health Check

```bash
curl http://localhost:8004/api/health
```

Përgjigja e pritur tregon statusin e të gjitha serviseve:
```json
{
  "status": "ok",
  "gateway": "operational",
  "services": {
    "auth": {"available": true, "url": "http://127.0.0.1:8000"},
    "resource": {"available": true, "url": "http://127.0.0.1:8001"},
    "monitoring": {"available": true, "url": "http://127.0.0.1:8002"},
    "autoscaler": {"available": true, "url": "http://127.0.0.1:8003"}
  }
}
```

### 2. Test Auth Service përmes Gateway

```bash
# Register
curl -X POST http://localhost:8004/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123"
  }'

# Login
curl -X POST http://localhost:8004/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### 3. Test Resource Service përmes Gateway

```bash
# Merrni token nga login
TOKEN="your-token-here"

# Merr të gjitha resurset
curl http://localhost:8004/api/resources/resources \
  -H "Authorization: Bearer $TOKEN"

# Krijo resurs të ri
curl -X POST http://localhost:8004/api/resources/resources \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "test-resource",
    "type": "vm",
    "cpu_capacity": 10,
    "memory_capacity": 1000,
    "storage_capacity": 50
  }'
```

### 4. Test Monitoring Service përmes Gateway

```bash
curl http://localhost:8004/api/monitoring/api/metrics \
  -H "Authorization: Bearer $TOKEN"
```

### 5. Test Autoscaler Service përmes Gateway

```bash
# Status (pa auth)
curl http://localhost:8004/api/autoscaler/api/status

# Analyze (pa auth)
curl http://localhost:8004/api/autoscaler/api/analyze

# History (kërkon admin role)
curl http://localhost:8004/api/autoscaler/api/history \
  -H "Authorization: Bearer $TOKEN"
```

## Testimi i Komunikimit të Brendshëm

### 1. Test Autoscaler → Monitoring Service

Autoscaler service duhet të jetë në gjendje të marrë metrikat nga monitoring-service:

```bash
# Kontrollo nëse autoscaler po merr metrikat
curl http://localhost:8003/api/status
```

Nëse `current_metrics` nuk është null, komunikimi funksionon.

### 2. Test Autoscaler → Resource Service

Autoscaler service duhet të jetë në gjendje të krijojë dhe fshijë resurset:

```bash
# Kontrollo status
curl http://localhost:8003/api/status
```

Nëse `total_resources` tregon numrin e saktë të resurseve, komunikimi funksionon.

## Testimi i Auto-Scaling

### 1. Simuloni ngarkesë të lartë

Për të testuar auto-scaling, duhet të simuloni ngarkesë të lartë në sistem. Kjo mund të bëhet duke:

1. Krijuar shumë resurset manualisht
2. Ose duke modifikuar metrikat në monitoring-service (nëse ka mundësi)

### 2. Monitoroni autoscaler

```bash
# Ekzekuto komandën e monitoring
cd autoscaler-service
php artisan autoscaler:monitor
```

Ose kontrollo log-et:
```bash
tail -f autoscaler-service/storage/logs/laravel.log
```

### 3. Kontrollo historikun

```bash
curl http://localhost:8003/api/history \
  -H "Authorization: Bearer $TOKEN"
```

## Troubleshooting

### Problem: Autoscaler nuk merr metrikat

1. Kontrollo nëse monitoring-service është duke funksionuar:
   ```bash
   curl http://localhost:8002/api/metrics
   ```

2. Kontrollo konfigurimin në `autoscaler-service/config/autoscaler.php`:
   ```php
   'monitoring' => [
       'service_url' => env('MONITORING_SERVICE_URL', 'http://127.0.0.1:8002'),
   ],
   ```

### Problem: Autoscaler nuk krijon resurset

1. Kontrollo nëse resource-service është duke funksionuar:
   ```bash
   curl http://localhost:8001/api/resources
   ```

2. Kontrollo konfigurimin në `autoscaler-service/config/autoscaler.php`:
   ```php
   'resource' => [
       'service_url' => env('RESOURCE_SERVICE_URL', 'http://127.0.0.1:8001'),
   ],
   ```

### Problem: API Gateway nuk rutejë kërkesat

1. Kontrollo konfigurimin në `api-gateway/config/gateway.php`
2. Kontrollo log-et:
   ```bash
   tail -f api-gateway/storage/logs/laravel.log
   ```

## Test Script i Plotë

Krijo një skript bash për testimin automatik:

```bash
#!/bin/bash

echo "Testing Cloud Platform Services..."
echo ""

echo "1. Testing Auth Service..."
curl -s http://localhost:8000/up | jq .

echo ""
echo "2. Testing Resource Service..."
curl -s http://localhost:8001/up | jq .

echo ""
echo "3. Testing Monitoring Service..."
curl -s http://localhost:8002/api/metrics | jq .

echo ""
echo "4. Testing Autoscaler Service..."
curl -s http://localhost:8003/api/status | jq .

echo ""
echo "5. Testing API Gateway..."
curl -s http://localhost:8004/api/health | jq .

echo ""
echo "All tests completed!"
```

Ruaje si `test-services.sh` dhe ekzekuto:
```bash
chmod +x test-services.sh
./test-services.sh
```

