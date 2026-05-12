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
