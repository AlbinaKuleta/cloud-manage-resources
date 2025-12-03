# Arkitektura dhe Funksionaliteti i Cloud Platform

## 📋 Përmbledhje

Cloud Platform është një sistem i ndërtuar me arkitekturë mikroservise që menaxhon resurset e cloud, monitoron performancën, dhe skalon automatikisht bazuar në ngarkesën e sistemit.

## 🏗️ Arkitektura e Mikroserviseve

```
┌─────────────────────────────────────────────────────────────┐
│                      API Gateway (8004)                      │
│  - Rutejë të gjitha kërkesat                                 │
│  - Authentication & Authorization                            │
│  - Rate Limiting                                             │
│  - Request Logging                                           │
└────────────┬─────────────────────────────────────────────────┘
             │
    ┌────────┴────────┬──────────────┬──────────────┬──────────────┐
    │                 │              │              │              │
┌───▼────┐    ┌───────▼────┐  ┌──────▼─────┐  ┌──────▼─────┐  ┌──────▼─────┐
│ Auth   │    │ Resource  │  │ Monitoring │  │ Autoscaler │  │  (Future) │
│ Service│    │  Service  │  │  Service   │  │  Service   │  │  Services  │
│ (8000) │    │  (8001)    │  │  (8002)    │  │  (8003)    │  │            │
└────────┘    └────────────┘  └────────────┘  └────────────┘  └────────────┘
```

## 🔧 Serviset dhe Funksionaliteti i Secilit

### 1. Auth Service (Port 8000)

**Qëllimi:** Menaxhon autentifikimin dhe autorizimin e përdoruesve.

**Funksionaliteti:**
- ✅ Regjistrim i përdoruesve të rinj
- ✅ Login/Logout
- ✅ Gjenerim dhe validim i JWT token-ave
- ✅ Menaxhim i role-ave (admin, editor, viewer)
- ✅ RBAC (Role-Based Access Control)

**Endpoints:**
```
POST   /api/register    - Regjistro përdorues të ri
POST   /api/login       - Login dhe merr token
POST   /api/logout      - Logout
GET    /api/me          - Merr informacionin e përdoruesit aktual
```

**Shembull i Përdorimit:**
```bash
# 1. Regjistro përdorues të ri
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
  }'

# 2. Login dhe merr token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
# Përgjigja: { "token": "eyJ0eXAiOiJKV1QiLCJhbGc...", "user": {...} }

# 3. Përdor token për kërkesa të tjera
curl http://localhost:8000/api/me \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

**Komunikim me Serviset e Tjera:**
- API Gateway e përdor për validimin e token-ave
- Të gjitha serviset e tjera marrin token nga Auth Service përmes API Gateway

---

### 2. Resource Service (Port 8001)

**Qëllimi:** Menaxhon resurset e cloud (VM, storage, network, etj.).

**Funksionaliteti:**
- ✅ Krijim, lexim, përditësim, fshirje resursesh (CRUD)
- ✅ Menaxhim i kapaciteteve (CPU, Memory, Storage)
- ✅ Tracking i rezervimeve të resurseve
- ✅ UI me Livewire për menaxhimin e resurseve
- ✅ Integrim me Monitoring Service për kapacitetin

**Endpoints API:**
```
GET    /api/resources           - Merr të gjitha resurset
GET    /api/resources/{id}      - Merr një resurs specifik
POST   /api/resources           - Krijo resurs të ri
PUT    /api/resources/{id}      - Përditëso resurs
DELETE /api/resources/{id}      - Fshi resurs
```

**Shembull i Përdorimit:**
```bash
TOKEN="your-auth-token"

# 1. Merr të gjitha resurset
curl http://localhost:8001/api/resources \
  -H "Authorization: Bearer $TOKEN"

# 2. Krijo resurs të ri (VM)
curl -X POST http://localhost:8001/api/resources \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "web-server-01",
    "type": "vm",
    "cpu_capacity": 20,
    "memory_capacity": 2000,
    "storage_capacity": 100,
    "region": "eu-west",
    "status": "active"
  }'

# 3. Përditëso resurs
curl -X PUT http://localhost:8001/api/resources/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "cpu_capacity": 30,
    "memory_capacity": 4000
  }'

# 4. Fshi resurs
curl -X DELETE http://localhost:8001/api/resources/1 \
  -H "Authorization: Bearer $TOKEN"
```

**Komunikim me Serviset e Tjera:**
- **Monitoring Service:** Kontrollon kapacitetin para krijimit të resursit
- **Autoscaler Service:** Krijon/fshin resurset automatikisht bazuar në ngarkesën

**Shembull i Komunikimit:**
```php
// Kur autoscaler krijon resurs të ri
POST http://localhost:8001/api/resources
{
  "name": "auto-scaled-20250101120000",
  "type": "vm",
  "cpu_capacity": 5,
  "memory_capacity": 500,
  "storage_capacity": 10,
  "status": "active"
}
```

---

### 3. Monitoring Service (Port 8002)

**Qëllimi:** Monitoron metrikat e sistemit në kohë reale.

**Funksionaliteti:**
- ✅ Koleksion i metrikave të sistemit (CPU, Memory, Disk)
- ✅ Dashboard për monitoring në kohë reale
- ✅ API për metrikat aktuale
- ✅ Verifikim i kapacitetit të disponueshëm

**Endpoints:**
```
GET    /api/metrics              - Merr metrikat aktuale të sistemit
GET    /metrics                  - Dashboard UI për monitoring
```

**Shembull i Përdorimit:**
```bash
# Merr metrikat aktuale
curl http://localhost:8002/api/metrics

# Përgjigja:
{
  "cpu_usage": 65.5,
  "memory_usage": 72.3,
  "disk_usage": 58.1,
  "timestamp": "2025-01-01T12:00:00Z"
}
```

**Komunikim me Serviset e Tjera:**
- **Autoscaler Service:** Merr metrikat për vendime scaling
- **Resource Service:** Verifikon kapacitetin para krijimit të resursit

**Shembull i Komunikimit:**
```php
// Autoscaler kërkon metrikat çdo 30 sekonda
GET http://localhost:8002/api/metrics

// Resource Service kontrollon kapacitetin
POST http://localhost:8002/api/check-capacity
{
  "cpu": 20,
  "memory": 2000,
  "storage": 100
}
// Përgjigja: { "available": true/false, "message": "..." }
```

---

### 4. Autoscaler Service (Port 8003)

**Qëllimi:** Skalon automatikisht resurset bazuar në ngarkesën e sistemit.

**Funksionaliteti:**
- ✅ Monitoron metrikat nga Monitoring Service
- ✅ Analizon ngarkesën e sistemit
- ✅ Scale Up: Krijon resurset kur ngarkesa është e lartë
- ✅ Scale Down: Fshin resurset kur ngarkesa është e ulët
- ✅ Historik i veprimeve të scaling
- ✅ Ekzekuton automatikisht çdo 30 sekonda

**Endpoints:**
```
GET    /api/status               - Status i autoscaler dhe metrikat
GET    /api/analyze              - Analizë e metrikave dhe vendim scaling
GET    /api/history              - Historiku i scaling (kërkon auth)
POST   /api/scale-up             - Scale up manual (kërkon auth)
POST   /api/scale-down           - Scale down manual (kërkon auth)
```

**Shembull i Përdorimit:**
```bash
# 1. Kontrollo status
curl http://localhost:8003/api/status

# Përgjigja:
{
  "status": "operational",
  "enabled": true,
  "current_metrics": {
    "cpu_usage": 85.5,
    "memory_usage": 90.2,
    "disk_usage": 75.1
  },
  "auto_scaled_resources_count": 2,
  "total_resources": 5,
  "thresholds": {
    "scale_up": {
      "cpu": 80,
      "memory": 85,
      "disk": 90,
      "duration": 120
    },
    "scale_down": {
      "cpu": 50,
      "memory": 50,
      "disk": 50,
      "duration": 300
    }
  }
}

# 2. Analizo metrikat
curl http://localhost:8003/api/analyze

# Përgjigja (nëse duhet scaling):
{
  "analysis": {
    "action": "scale_up",
    "reason": "CPU: 85.5% > 80%, Memory: 90.2% > 85%",
    "metrics": {
      "cpu": 85.5,
      "memory": 90.2,
      "disk": 75.1
    }
  }
}

# 3. Scale up manual
TOKEN="your-admin-token"
curl -X POST http://localhost:8003/api/scale-up \
  -H "Authorization: Bearer $TOKEN"

# 4. Shiko historikun
curl http://localhost:8003/api/history \
  -H "Authorization: Bearer $TOKEN"
```

**Komunikim me Serviset e Tjera:**

1. **Monitoring Service → Autoscaler:**
```php
// Çdo 30 sekonda, autoscaler kërkon metrikat
GET http://localhost:8002/api/metrics
// Përgjigja: { "cpu_usage": 85, "memory_usage": 90, ... }
```

2. **Autoscaler → Resource Service (Scale Up):**
```php
// Kur ngarkesa është e lartë për 2 minuta, krijon resurs të ri
POST http://localhost:8001/api/resources
{
  "name": "auto-scaled-20250101120000",
  "type": "vm",
  "cpu_capacity": 5,
  "memory_capacity": 500,
  "storage_capacity": 10,
  "status": "active"
}
```

3. **Autoscaler → Resource Service (Scale Down):**
```php
// Kur ngarkesa është e ulët për 5 minuta, fshin resursin më të vjetër
DELETE http://localhost:8001/api/resources/5
```

**Logjika e Scaling:**

```
┌─────────────────────────────────────────────────────────┐
│  Monitoring Service (çdo 30 sekonda)                    │
│  └─> Merr metrikat: CPU=85%, Memory=90%, Disk=75%      │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│  Autoscaler Service - Analizë                           │
│  ├─ CPU > 80%? ✅                                       │
│  ├─ Memory > 85%? ✅                                    │
│  ├─ Ka kaluar 2 minuta? ✅                               │
│  └─> VENDIM: SCALE UP                                    │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│  Resource Service                                        │
│  └─> Krijo resurs të ri: auto-scaled-20250101120000    │
└─────────────────────────────────────────────────────────┘
```

---

### 5. API Gateway (Port 8004)

**Qëllimi:** Pika e hyrjes e vetme për të gjitha kërkesat API.

**Funksionaliteti:**
- ✅ Routing i kërkesave në serviset e duhura
- ✅ Authentication middleware (validon token me Auth Service)
- ✅ Rate limiting (100 requests/minutë)
- ✅ Request logging
- ✅ Health check për të gjitha serviset
- ✅ Role-based access control

**Endpoints:**
```
GET    /api/health              - Health check për të gjitha serviset
POST   /api/auth/*              - Rutejë në Auth Service
GET    /api/resources/*         - Rutejë në Resource Service
GET    /api/monitoring/*        - Rutejë në Monitoring Service
GET    /api/autoscaler/*        - Rutejë në Autoscaler Service
```

**Shembull i Përdorimit:**
```bash
# 1. Health check
curl http://localhost:8004/api/health

# Përgjigja:
{
  "status": "ok",
  "gateway": "operational",
  "services": {
    "auth": {
      "available": true,
      "url": "http://127.0.0.1:8000"
    },
    "resource": {
      "available": true,
      "url": "http://127.0.0.1:8001"
    },
    "monitoring": {
      "available": true,
      "url": "http://127.0.0.1:8002"
    },
    "autoscaler": {
      "available": true,
      "url": "http://127.0.0.1:8003"
    }
  }
}

# 2. Login përmes Gateway
curl -X POST http://localhost:8004/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'

# 3. Merr resurset përmes Gateway
TOKEN="your-token"
curl http://localhost:8004/api/resources/resources \
  -H "Authorization: Bearer $TOKEN"

# 4. Merr metrikat përmes Gateway
curl http://localhost:8004/api/monitoring/api/metrics \
  -H "Authorization: Bearer $TOKEN"

# 5. Kontrollo autoscaler status përmes Gateway
curl http://localhost:8004/api/autoscaler/api/status
```

**Komunikim me Serviset e Tjera:**

Gateway komunikon me të gjitha serviset:

```
┌─────────────────────────────────────────────────────────┐
│  Client Request                                          │
│  GET /api/resources/resources                           │
│  Authorization: Bearer token123                          │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│  API Gateway                                            │
│  1. Validon token me Auth Service                        │
│  2. Kontrollon rate limiting                             │
│  3. Loggon kërkesën                                      │
│  4. Rutejë në Resource Service                          │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│  Resource Service                                        │
│  └─> Kthen përgjigjen                                   │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│  API Gateway                                            │
│  └─> Kthen përgjigjen te klienti                        │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 Rrjedha e Komunikimit - Shembuj Konkrete

### Shembull 1: Përdorues krijon resurs të ri

```
1. Klienti → API Gateway
   POST /api/resources/resources
   Authorization: Bearer token123
   Body: { "name": "web-server", "cpu_capacity": 20, ... }

2. API Gateway → Auth Service
   GET /api/me
   Authorization: Bearer token123
   Përgjigja: { "user": { "id": 1, "role": "admin" } }

3. API Gateway → Resource Service
   POST /api/resources
   Authorization: Bearer token123
   Body: { "name": "web-server", ... }

4. Resource Service → Monitoring Service
   POST /api/check-capacity
   Body: { "cpu": 20, "memory": 2000, "storage": 100 }
   Përgjigja: { "available": true }

5. Resource Service → Database
   INSERT INTO resources ...

6. Resource Service → API Gateway
   Përgjigja: { "success": true, "data": { "id": 1, ... } }

7. API Gateway → Klienti
   Përgjigja: { "success": true, "data": { "id": 1, ... } }
```

### Shembull 2: Auto-scaling në veprim

```
1. Autoscaler Service (çdo 30 sekonda)
   └─> Ekzekuton: php artisan autoscaler:monitor

2. Autoscaler → Monitoring Service
   GET /api/metrics
   Përgjigja: { "cpu_usage": 85, "memory_usage": 90, "disk_usage": 75 }

3. Autoscaler analizon:
   - CPU 85% > 80% (threshold) ✅
   - Memory 90% > 85% (threshold) ✅
   - Ka kaluar 2 minuta? ✅
   - Vendim: SCALE UP

4. Autoscaler → Resource Service
   POST /api/resources
   Body: {
     "name": "auto-scaled-20250101120000",
     "type": "vm",
     "cpu_capacity": 5,
     "memory_capacity": 500,
     "storage_capacity": 10
   }

5. Resource Service → Database
   INSERT INTO resources ...

6. Autoscaler → Database
   INSERT INTO scaling_history (action='scale_up', ...)

7. Log:
   "Autoscaler: Scale up u ekzekutua me sukses"
```

### Shembull 3: Përdorues shikon dashboard

```
1. Klienti → API Gateway
   GET /api/resources/resources
   Authorization: Bearer token123

2. API Gateway → Auth Service
   GET /api/me
   Authorization: Bearer token123
   Përgjigja: { "user": { "id": 1, "role": "viewer" } }

3. API Gateway → Resource Service
   GET /api/resources
   Authorization: Bearer token123

4. Resource Service → Database
   SELECT * FROM resources

5. Resource Service → API Gateway
   Përgjigja: { "data": [{ "id": 1, "name": "web-server", ... }] }

6. API Gateway → Klienti
   Përgjigja: { "data": [{ "id": 1, "name": "web-server", ... }] }
```

---

## 📊 Diagrami i Komunikimit

```
                    ┌──────────────┐
                    │   Client     │
                    └──────┬───────┘
                           │
                           ▼
              ┌──────────────────────┐
              │   API Gateway (8004) │
              │  - Auth Validation   │
              │  - Rate Limiting     │
              │  - Request Routing   │
              └──────┬───────────────┘
                     │
        ┌────────────┼────────────┬──────────────┐
        │            │            │              │
        ▼            ▼            ▼              ▼
┌───────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐
│   Auth    │  │ Resource │  │Monitoring│  │Autoscaler│
│  (8000)   │  │  (8001)  │  │  (8002)  │  │  (8003)  │
└───────────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘
                     │              │              │
                     │              │              │
                     │              │◄─────────────┤
                     │              │  Metrikat    │
                     │              │              │
                     │◄─────────────┤              │
                     │  Kapaciteti  │              │
                     │              │              │
                     │              │              │
                     ▼              ▼              ▼
              ┌──────────────────────────────────┐
              │        Database                  │
              │  - users                         │
              │  - resources                     │
              │  - scaling_history               │
              └──────────────────────────────────┘
```

---

## 🔐 Siguria dhe Autentifikimi

### Flow i Autentifikimit:

```
1. Klienti → API Gateway
   POST /api/auth/login
   { "email": "user@example.com", "password": "..." }

2. API Gateway → Auth Service
   POST /api/login
   { "email": "user@example.com", "password": "..." }

3. Auth Service → Database
   SELECT * FROM users WHERE email = ...

4. Auth Service → API Gateway
   { "token": "eyJ0eXAiOiJKV1QiLCJhbGc...", "user": {...} }

5. API Gateway → Klienti
   { "token": "eyJ0eXAiOiJKV1QiLCJhbGc...", "user": {...} }

6. Klienti → API Gateway (kërkesa të tjera)
   GET /api/resources/resources
   Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...

7. API Gateway → Auth Service
   GET /api/me
   Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...

8. Auth Service → API Gateway
   { "user": { "id": 1, "role": "admin" } }

9. API Gateway → Resource Service
   GET /api/resources
   Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

---

## 📈 Monitoring dhe Auto-Scaling

### Threshold-e dhe Vendime:

**Scale Up (Krijon resurset):**
- CPU > 80% për 2 minuta
- Memory > 85% për 2 minuta
- Disk > 90% për 2 minuta
- Maksimum: 10 resurset auto-scaled

**Scale Down (Fshin resurset):**
- CPU < 50% për 5 minuta
- Memory < 50% për 5 minuta
- Disk < 50% për 5 minuta
- Minimum: 1 resurs auto-scaled

### Shembull i Plotë i Auto-Scaling:

```
Koha: 12:00:00
Metrikat: CPU=60%, Memory=65%, Disk=55%
Vendim: Nuk ka nevojë për scaling

Koha: 12:01:00
Metrikat: CPU=82%, Memory=87%, Disk=70%
Vendim: Fillon monitoring (2 minuta)

Koha: 12:02:00
Metrikat: CPU=85%, Memory=90%, Disk=72%
Vendim: SCALE UP (ka kaluar 2 minuta)
Aksion: Krijo resurs të ri "auto-scaled-20250101120200"

Koha: 12:10:00
Metrikat: CPU=45%, Memory=48%, Disk=40%
Vendim: Fillon monitoring (5 minuta)

Koha: 12:15:00
Metrikat: CPU=42%, Memory=45%, Disk=38%
Vendim: SCALE DOWN (ka kaluar 5 minuta)
Aksion: Fshi resursin më të vjetër "auto-scaled-20250101120200"
```

---

## 🎯 Përdorimi i Projektit

### Scenario 1: Startup i një aplikacioni të ri

```
1. Përdoruesi regjistrohet përmes API Gateway
2. Përdoruesi krijon resurset e nevojshme (web server, database, etj.)
3. Monitoring Service monitoron performancën
4. Kur ngarkesa rritet, Autoscaler krijon resurset shtesë automatikisht
5. Kur ngarkesa zvogëlohet, Autoscaler fshin resurset e tepërta
```

### Scenario 2: Peak traffic (Black Friday, etj.)

```
1. Ngarkesa rritet shpejt: CPU 60% → 85% në 1 minutë
2. Autoscaler detekton ngarkesën e lartë
3. Pas 2 minutash, Autoscaler krijon 3 resurset shtesë
4. Sistemi tani ka kapacitet për të trajtuar ngarkesën
5. Pas peak traffic, ngarkesa bie në 40%
6. Pas 5 minutash, Autoscaler fshin resurset e tepërta
```

### Scenario 3: Monitoring dhe Optimizim

```
1. Admin shikon dashboard në Resource Service
2. Shikon metrikat në kohë reale nga Monitoring Service
3. Shikon historikun e scaling nga Autoscaler Service
4. Optimizon threshold-e bazuar në përvojë
5. Krijo resurset manualisht nëse nevojitet
```

---

## 🛠️ Teknologjitë e Përdorura

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Livewire, Tailwind CSS
- **Database:** SQLite (mund të përdoret MySQL/PostgreSQL)
- **Authentication:** Laravel Sanctum (JWT tokens)
- **HTTP Client:** Guzzle (për komunikim ndërmjet serviseve)
- **Scheduling:** Laravel Task Scheduler (për auto-scaling)

---

## 📝 Konkluzion

Ky projekt demonstron një arkitekturë të plotë mikroserviseve me:
- ✅ Ndarje të qartë të përgjegjësive
- ✅ Komunikim HTTP RESTful ndërmjet serviseve
- ✅ Centralizim i autentifikimit përmes API Gateway
- ✅ Auto-scaling inteligjent bazuar në metrikat reale
- ✅ Monitoring dhe logging të plotë
- ✅ Siguri dhe rate limiting

Të gjitha serviset janë të pavarura dhe mund të shkallëzohen veçmas, duke e bërë sistemin fleksibël dhe të qëndrueshëm.

