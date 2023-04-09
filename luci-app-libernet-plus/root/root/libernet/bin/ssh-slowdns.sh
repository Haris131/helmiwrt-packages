#!/bin/bash

# SSH Connector Wrapper
# by Lutfa Ilham
# v1.0.0

if [ "$(id -u)" != "0" ]; then
  echo "This script must be run as root" 1>&2
  exit 1
fi

SERVICE_NAME="SSH"
SYSTEM_CONFIG="${LIBERNET_DIR}/system/config.json"
SSH_PROFILE="$(grep 'ssh_slowdns":' ${SYSTEM_CONFIG}  | awk '{print $2}' | sed 's/,//g; s/"//g')"
SSH_CONFIG="${LIBERNET_DIR}/bin/config/ssh_slowdns/${SSH_PROFILE}.json"
SSH_HOST="$(grep 'host":' ${SSH_CONFIG} | awk '{print $2}' | sed 's/,//g; s/"//g')"
SSH_USER="$(grep 'username":' ${SSH_CONFIG} | awk '{print $2}' | sed 's/,//g; s/"//g')"
SSH_PASS="$(grep 'password":' ${SSH_CONFIG} | awk '{print $2}' | sed 's/,//g; s/"//g')"
DYNAMIC_PORT="$(grep 'port":' ${SYSTEM_CONFIG} | awk '{print $2}' | sed 's/,//g; s/"//g' | sed -n '1p')"

function run() {
  cd "${LIBERNET_DIR}/log"
  echo "" > screenlog.0
  # write to service log
  "${LIBERNET_DIR}/bin/log.sh" -w "Config: ${SSH_PROFILE}, Mode: ${SERVICE_NAME}"
  "${LIBERNET_DIR}/bin/slowdns.sh" -r \
    && "${LIBERNET_DIR}/bin/log.sh" -w "Starting ${SERVICE_NAME} service" \
    && echo -e "Starting ${SERVICE_NAME} service ..." \
    && screen -L -AmdS ssh-connector "${LIBERNET_DIR}/bin/ssh-loop.sh" -s "${SSH_USER}" "${SSH_PASS}" "${SSH_HOST}" "${DYNAMIC_PORT}" \
    && echo -e "${SERVICE_NAME} service started!"
}

function stop() {
  echo "" > "${LIBERNET_DIR}/log/screenlog.0"
  # write to service log
  "${LIBERNET_DIR}/bin/log.sh" -w "Stopping ${SERVICE_NAME} service"
  echo -e "Stopping ${SERVICE_NAME} service ..."
  kill $(screen -list | grep ssh-connector | awk -F '[.]' {'print $1'})
  "${LIBERNET_DIR}/bin/slowdns.sh" -s
  echo -e "${SERVICE_NAME} service stopped!"
}

function usage() {
  cat <<EOF
Usage:
  -r  Run ${SERVICE_NAME} service
  -s  Stop ${SERVICE_NAME} service
EOF
}

case "${1}" in
  -r)
    run
    ;;
  -s)
    stop
    ;;
  *)
    usage
    ;;
esac
