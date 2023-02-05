FROM node:13.12.0-alpine As builder

WORKDIR /app
ENV PATH /app/node_modules/.bin:$PATH

COPY package.json ./
COPY package-lock.json ./
RUN npm install
# RUN npm install react-script@4.0.3 -g --silent
COPY . ./

CMD ["npm","start"]
