FROM node:latest

WORKDIR /usr/src/app

COPY package.json yarn.lock ./

ENV NODE_ENV production
RUN yarn install

COPY . .

EXPOSE 3200
CMD ["node", "bin/wwww"]
