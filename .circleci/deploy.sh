#!/usr/bin/env bash

err() {
  echo 1>&2 "$@";
}

main() {
  set -e
  trap 'echo "Error occurred at line ${LINENO}"' ERR
  declare -r environment="${1}"

  if [[ ${environment} = "INTG" ]]
  then
    deploy_intg
  elif [[ ${environment} = "STGE" ]]
  then
    deploy_stge
  elif [[ ${environment} = "PROD" ]]
  then
    deploy_prod
  else
    err "Unknown environment ${environment}"
  fi
}

install_circle_scripts() {
  echo "install circle-scripts, see https://github.com/infusionsoft/circle-scripts#circle-scripts"
  curl "https://storage.googleapis.com/circle-scripts/latest/init.sh" 2>/dev/null | bash 2>/dev/null
}

deploy_intg() {
  install_circle_scripts || echo "install_circle_scripts returned $?"
  echo $INTG_GAE_SERVICE_ACCOUNT | base64 -d >> /tmp/intg.json
  gcloud auth activate-service-account --key-file=/tmp/intg.json
  export GOOGLE_APPLICATION_CREDENTIALS=/tmp/intg.json
  version=$(get_deployable_artifact_version)
  deploy_roles deploywebcam intg $version
}

deploy_stge() {
  install_circle_scripts || echo "install_circle_scripts returned $?"
  echo $STGE_GAE_SERVICE_ACCOUNT | base64 -d >> /tmp/stge.json
  gcloud auth activate-service-account --key-file=/tmp/stge.json
  export GOOGLE_APPLICATION_CREDENTIALS=/tmp/stge.json
  version=$(get_deployable_artifact_version)
  deploy_roles deploywebcam stge $version
}

deploy_prod() {
  install_circle_scripts || echo "install_circle_scripts returned $?"
  echo $PROD_GAE_SERVICE_ACCOUNT | base64 -d >> /tmp/prod.json
  gcloud auth activate-service-account --key-file=/tmp/prod.json
  export GOOGLE_APPLICATION_CREDENTIALS=/tmp/prod.json
  version=$(get_deployable_artifact_version)
  deploy_roles deploywebcam prod $version
}

get_deployable_artifact_version() {
  last_tag=$(git describe --tags --match v* --abbrev=0)
  echo ${last_tag#v}
}

main $1
