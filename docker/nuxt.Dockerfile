FROM node:20.10-alpine as builder

ENV NUXT_HOST=0.0.0.0
ENV NUXT_PORT=3000

COPY src/nuxt /app
WORKDIR /app

RUN yarn
RUN yarn build

FROM node:20.10-alpine as runner

COPY --from=builder /app/.output /app
WORKDIR /app

EXPOSE 3000

CMD ["node", "/app/server/index.mjs"]