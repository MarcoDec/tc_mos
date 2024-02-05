FROM node:14-alpine as build

ARG API_HOST

WORKDIR /app
COPY package.json ./
COPY yarn.lock ./
RUN yarn install --network-timeout 1000000
WORKDIR /app
RUN ls -l
COPY package.json ./
COPY yarn.lock ./
RUN yarn --pure-lockfile --non-interactive
COPY src/ ./src
COPY .env ./
RUN echo "REACT_APP_DOMAIN = $API_HOST" > .env
COPY public/ ./public
RUN yarn build

# production env
FROM nginx:stable-alpine
COPY --from=build /app/build /usr/share/nginx/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]