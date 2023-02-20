FROM node:14-alpine as build

ARG VUE_APP_API_URL_BASE
ARG VUE_APP_COMPANY
ARG VUE_APP_USER
ARG VUE_APP_USER_PASSWORD

WORKDIR /app
COPY package.json ./

WORKDIR /app
RUN ls -l
COPY package.json ./
RUN npm install
COPY src/ ./src
COPY .eslintrc.js ./
COPY babel.config.js ./
COPY .env ./
RUN echo -e "VUE_APP_API_URL_BASE = $VUE_APP_API_URL_BASE\nVUE_APP_COMPANY = $VUE_APP_COMPANY\nVUE_APP_USER = $VUE_APP_USER\nVUE_APP_USER_PASSWORD = $VUE_APP_USER_PASSWORD" > .env
COPY public/ ./public
RUN npm run build

# production env
FROM nginx:stable-alpine
COPY --from=build /app/dist /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]