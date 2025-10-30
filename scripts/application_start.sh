#!/bin/bash
# Install docker if not present
if ! command -v docker &> /dev/null
then
    apt-get update -y
    apt-get install -y docker.io
    systemctl start docker
    systemctl enable docker
    usermod -a -G docker ubuntu
fi

# --- REPLACE THESE VALUES ---
AWS_ACCOUNT_ID="<YOUR_12_DIGIT_AWS_ACCOUNT_ID>"
AWS_REGION="<YOUR_AWS_REGION_e.g.,_ap-south-1>"
IMAGE_REPO_NAME="bitcot-php-app"
DB_HOST="<YOUR_RDS_ENDPOINT>"

# --- SECURELY FETCH CREDENTIALS ---
# This is the professional way to handle secrets.
# It uses the EC2 instance's IAM Role to get the parameters.
DB_USER=$(aws ssm get-parameter --name "/bitcot-app/db-user" --with-decryption --region $AWS_REGION --query "Parameter.Value" --output text)
DB_PASS=$(aws ssm get-parameter --name "/bitcot-app/db-pass" --with-decryption --region $AWS_REGION --query "Parameter.Value" --output text)

# --- DO NOT EDIT BELOW THIS LINE ---
IMAGE_URI="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$IMAGE_REPO_NAME:latest"

# Login to ECR (works via IAM Role)
aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com

docker pull $IMAGE_URI
docker run -d -p 80:80 --name php-container -e DB_HOST=$DB_HOST -e DB_USER=$DB_USER -e DB_PASS=$DB_PASS -e DB_NAME=webappdb $IMAGE_URI