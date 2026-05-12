Below is a production-ready setup for your Laravel Video Calling Application using:

* Laravel Octane + RoadRunner
* Laravel Reverb
* MySQL
* Redis
* NGINX
* Docker Compose
* GitLab CI/CD
* Personal VPS Deployment

---

# 📄 `.env`

```env id="n2v8k5"
APP_NAME="Video Calling App"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# -----------------------------------
# Database
# -----------------------------------

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=video_calling
DB_USERNAME=video_user
DB_PASSWORD=strong_password

# -----------------------------------
# Redis
# -----------------------------------

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# -----------------------------------
# Broadcasting
# -----------------------------------

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=video-app
REVERB_APP_KEY=video-key
REVERB_APP_SECRET=video-secret

REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https

# -----------------------------------
# Octane
# -----------------------------------

OCTANE_SERVER=roadrunner

# -----------------------------------
# Mail
# -----------------------------------

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# -----------------------------------
# File Upload
# -----------------------------------

FILESYSTEM_DISK=public

# -----------------------------------
# Vite
# -----------------------------------

VITE_APP_NAME="${APP_NAME}"

# -----------------------------------
# WebRTC / Frontend
# -----------------------------------

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="your-domain.com"
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
```

---

# 🐳 `docker-compose.yml`

```yaml id="u8q3m1"
version: '3.9'

services:

  nginx:
    image: nginx:latest
    container_name: video-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app
      - reverb
    networks:
      - video-network

  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: video-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    command: >
      sh -c "
      php artisan config:cache &&
      php artisan route:cache &&
      php artisan view:cache &&
      php artisan migrate --force &&
      php artisan octane:start
      --server=roadrunner
      --host=0.0.0.0
      --port=8000
      "
    depends_on:
      - mysql
      - redis
    networks:
      - video-network

  reverb:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: video-reverb
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    command: php artisan reverb:start --host=0.0.0.0 --port=8080
    depends_on:
      - redis
    networks:
      - video-network

  queue:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: video-queue
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    command: php artisan queue:work --tries=3
    depends_on:
      - redis
      - mysql
    networks:
      - video-network

  scheduler:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: video-scheduler
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    command: php artisan schedule:work
    depends_on:
      - mysql
      - redis
    networks:
      - video-network

  mysql:
    image: mysql:8
    container_name: video-mysql
    restart: unless-stopped
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: video_calling
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: video_user
      MYSQL_PASSWORD: strong_password
    volumes:
      - mysql-data:/var/lib/mysql
    networks:
      - video-network

  redis:
    image: redis:7-alpine
    container_name: video-redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis-data:/data
    networks:
      - video-network

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: video-phpmyadmin
    restart: unless-stopped
    ports:
      - "8081:80"
    environment:
      PMA_HOST: mysql
      MYSQL_ROOT_PASSWORD: root_password
    depends_on:
      - mysql
    networks:
      - video-network

networks:
  video-network:
    driver: bridge

volumes:
  mysql-data:
  redis-data:
```

---

# 🌐 `docker/nginx/default.conf`

```nginx id="v1x7r4"
server {
    listen 80;
    server_name your-domain.com;

    client_max_body_size 500M;

    location / {
        proxy_pass http://app:8000;

        proxy_http_version 1.1;

        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    location /app {
        proxy_pass http://reverb:8080;

        proxy_http_version 1.1;

        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";

        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
}
```

---

# 🐳 `Dockerfile`

```dockerfile id="p5q2z8"
FROM php:8.3-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install \
    pdo_mysql \
    bcmath \
    intl \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

EXPOSE 8000

CMD ["php", "artisan", "octane:start", "--server=roadrunner", "--host=0.0.0.0", "--port=8000"]
```

---

# 🚀 GitLab CI/CD (`.gitlab-ci.yml`)

```yaml id="x8m2p4"
stages:
  - deploy

deploy_production:
  stage: deploy

  only:
    - main

  before_script:
    - apt-get update -y
    - apt-get install -y openssh-client

  script:
    - mkdir -p ~/.ssh
    - echo "$SSH_PRIVATE_KEY" > ~/.ssh/id_rsa
    - chmod 600 ~/.ssh/id_rsa

    - ssh -o StrictHostKeyChecking=no root@$SERVER_IP "
        cd /var/www/video-calling &&
        git pull origin main &&
        docker compose down &&
        docker compose up -d --build
      "
```

---

# 🔐 GitLab CI/CD Variables

Add these in GitLab:

| Variable               | Description              |
| ---------------------- | ------------------------ |
| `SSH_PRIVATE_KEY`      | VPS Private SSH Key      |
| `SERVER_IP`            | VPS Server IP            |
| `CI_REGISTRY_USER`     | GitLab Registry User     |
| `CI_REGISTRY_PASSWORD` | GitLab Registry Password |

---

# 🖥️ VPS Deployment Steps

## Install Docker

```bash id="a7v3m8"
curl -fsSL https://get.docker.com | sh
```

---

## Install Docker Compose

```bash id="s4x1q6"
apt install docker-compose-plugin -y
```

---

## Clone Project

```bash id="m9p2t5"
git clone git@gitlab.com:your-username/video-calling.git
```

---

## Start Project

```bash id="r6w8k1"
docker compose up -d --build
```

---

# 🔥 Production Recommendations

* Use Cloudflare Proxy
* Use SSL with Let's Encrypt
* Use Coturn Server for WebRTC
* Enable Redis Persistence
* Enable Laravel Horizon
* Use Supervisor for Critical Services
* Setup Daily Database Backups
* Use Fail2Ban for SSH Security

---

# 📡 Recommended Ports

| Service   | Port |
| --------- | ---- |
| HTTP      | 80   |
| HTTPS     | 443  |
| Reverb WS | 8080 |
| MySQL     | 3306 |
| Redis     | 6379 |
