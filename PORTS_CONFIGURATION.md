# Konfigurimi i Portave - Cloud Platform

Ky dokument përshkruan konfigurimin e portave për të gjitha serviset e platformës cloud.

## Portet e Serviseve

| Service | Port | URL Default |
|---------|------|-------------|
| auth-service | 8000 | http://127.0.0.1:8000 |
| resource-service | 8001 | http://127.0.0.1:8001 |
| monitoring-service | 8002 | http://127.0.0.1:8002 |
| autoscaler-service | 8003 | http://127.0.0.1:8003 |
| api-gateway | 8004 | http://127.0.0.1:8004 |

## Konfigurimi i Portave në Çdo Service

### 1. auth-service (Port 8000)

Nuk ka nevojë për konfigurim të veçantë - përdor portin 8000 si default.

```bash
cd auth-service
php artisan serve --port=8000
```

### 2. resource-service (Port 8001)

```bash
cd resource-service
php artisan serve --port=8001
```

### 3. monitoring-service (Port 8002)

```bash
cd monitoring-service
php artisan serve --port=8002
```

### 4. autoscaler-service (Port 8003)

Konfigurimi në `autoscaler-service/config/autoscaler.php`:
```php
'monitoring' => [
    'service_url' => env('MONITORING_SERVICE_URL', 'http://127.0.0.1:8002'),
],

'resource' => [
    'service_url' => env('RESOURCE_SERVICE_URL', 'http://127.0.0.1:8001'),
],
```

Startimi:
```bash
cd autoscaler-service
php artisan serve --port=8003
```

### 5. api-gateway (Port 8004)

Konfigurimi në `api-gateway/config/gateway.php`:
```php
'services' => [
    'auth' => [
        'url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:8000'),
    ],
    'resource' => [
        'url' => env('RESOURCE_SERVICE_URL', 'http://127.0.0.1:8001'),
    ],
    'monitoring' => [
        'url' => env('MONITORING_SERVICE_URL', 'http://127.0.0.1:8002'),
    ],
    'autoscaler' => [
        'url' => env('AUTOSCALER_SERVICE_URL', 'http://127.0.0.1:8003'),
    ],
],
```

Startimi:
```bash
cd api-gateway
php artisan serve --port=8004
```

## Komunikimi Ndërmjet Serviseve

### Autoscaler Service → Monitoring Service
- Autoscaler komunikon me monitoring-service në portin 8002
- Endpoint: `GET http://127.0.0.1:8002/api/metrics`

### Autoscaler Service → Resource Service
- Autoscaler komunikon me resource-service në portin 8001
- Endpoints:
  - `GET http://127.0.0.1:8001/api/resources` - Merr të gjitha resurset
  - `POST http://127.0.0.1:8001/api/resources` - Krijo resurs të ri
  - `DELETE http://127.0.0.1:8001/api/resources/{id}` - Fshi resurs

### API Gateway → Të gjitha serviset
- API Gateway rutejë kërkesat në të gjitha serviset:
  - Auth: `http://127.0.0.1:8000`
  - Resource: `http://127.0.0.1:8001`
  - Monitoring: `http://127.0.0.1:8002`
  - Autoscaler: `http://127.0.0.1:8003`

## Ndryshimi i Portave (nëse nevojitet)

Nëse doni të ndryshoni portet, duhet të:

1. **Ndryshoni komandën e startimit:**
   ```bash
   php artisan serve --port=NEW_PORT
   ```

2. **Ndryshoni konfigurimin në .env files** (nëse ekzistojnë):
   ```env
   AUTH_SERVICE_URL=http://127.0.0.1:NEW_PORT
   RESOURCE_SERVICE_URL=http://127.0.0.1:NEW_PORT
   MONITORING_SERVICE_URL=http://127.0.0.1:NEW_PORT
   AUTOSCALER_SERVICE_URL=http://127.0.0.1:NEW_PORT
   ```

3. **Ose ndryshoni direkt në config files** siç u tregua më lart.

## Verifikimi i Portave

Për të verifikuar që të gjitha portet janë në përdorim:

**Windows:**
```powershell
netstat -ano | findstr "8000 8001 8002 8003 8004"
```

**Linux/Mac:**
```bash
lsof -i :8000 -i :8001 -i :8002 -i :8003 -i :8004
```

Ose përdorni curl për të testuar çdo service:
```bash
curl http://localhost:8000/up  # auth-service
curl http://localhost:8001/up   # resource-service
curl http://localhost:8002/api/metrics  # monitoring-service
curl http://localhost:8003/api/status  # autoscaler-service
curl http://localhost:8004/api/health  # api-gateway
```

