FROM alpine:3.21 AS alpine-upstream


FROM alpine-upstream AS dist

COPY dist/clibitica-linux-*.tar.gz .

RUN mkdir -p /dist/amd64 /dist/arm64
RUN tar -C /dist/amd64 -xzf clibitica-linux-x86_64.tar.gz
RUN tar -C /dist/arm64 -xzf clibitica-linux-aarch64.tar.gz


FROM alpine-upstream

RUN apk add --no-cache shadow
RUN useradd -u 10000 -m -d /home/clibitica -s /bin/sh clibitica

ARG TARGETARCH
COPY --from=dist /dist/${TARGETARCH}/clibitica /usr/local/bin/clibitica

USER clibitica

RUN mkdir -p /home/clibitica/.cache/clibitica

VOLUME /home/clibitica/.cache/clibitica

ENTRYPOINT [ "/usr/local/bin/clibitica" ]
