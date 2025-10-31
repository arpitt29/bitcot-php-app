pipeline {
    agent any

    environment {
        AWS_ACCOUNT_ID = credentials('aws-account-id')   // Secret Text credential with 12-digit AWS ID
        AWS_REGION = 'ap-south-1'
        IMAGE_REPO_NAME = 'php-rds-app'
        IMAGE_TAG = 'latest'
        EC2_PUBLIC_IP = credentials('ec2-public-ip')     // Secret Text credential
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/arpitt29/bitcot-php-app.git'
            }
        }

        stage('Build Docker Image') {
            steps {
                sh "docker build -t ${IMAGE_REPO_NAME}:${IMAGE_TAG} ."
            }
        }

        stage('Login to AWS ECR') {
            steps {
                withCredentials([
                    usernamePassword(
                        credentialsId: 'aws-creds',
                        usernameVariable: 'AWS_ACCESS_KEY_ID',
                        passwordVariable: 'AWS_SECRET_ACCESS_KEY'
                    )
                ]) {
                    sh '''
                      set -e
                      export AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID
                      export AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY
                      export AWS_REGION=${AWS_REGION}

                      echo "🔐 Logging into Amazon ECR..."
                      aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com
                    '''
                }
            }
        }

        stage('Push Image to ECR') {
            steps {
                sh '''
                  set -e
                  export ECR_URL=$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$IMAGE_REPO_NAME:$IMAGE_TAG

                  docker tag $IMAGE_REPO_NAME:$IMAGE_TAG $ECR_URL
                  docker push $ECR_URL
                '''
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent (credentials: ['ec2-ssh']) {
                    sh '''
                      set -e
                      export ECR_URL=$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$IMAGE_REPO_NAME:$IMAGE_TAG

                      ssh -o StrictHostKeyChecking=no ubuntu@$EC2_PUBLIC_IP << EOF
                        set -e
                        aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com
                        docker pull $ECR_URL
                        docker stop php-rds-app || true
                        docker rm php-rds-app || true
                        docker run -d --name php-rds-app -p 80:80 --env-file /home/ubuntu/.env $ECR_URL
                      EOF
                    '''
                }
            }
        }
    }

    post {
        success { echo '✅ Deployment successful' }
        failure { echo '❌ Deployment failed — check console output' }
    }
}
