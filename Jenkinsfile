pipeline {
    agent any

    environment {
        AWS_ACCOUNT_ID = '<AWS_ACCOUNT_ID>'
        AWS_REGION = '<AWS_REGION>'
        IMAGE_REPO_NAME = 'php-rds-app'
        IMAGE_TAG = 'latest'
        ECR_URL = "${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${IMAGE_REPO_NAME}:${IMAGE_TAG}"
        EC2_PUBLIC_IP = '<EC2_PUBLIC_IP>'
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'main', url: 'https://github.com/<your-username>/<your-repo>.git'
            }
        }

        stage('Build Docker Image') {
            steps {
                sh "docker build -t ${IMAGE_REPO_NAME}:${IMAGE_TAG} ."
            }
        }

        stage('Login to AWS ECR') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'aws-creds', usernameVariable: 'AWS_ACCESS_KEY_ID', passwordVariable: 'AWS_SECRET_ACCESS_KEY')]) {
                    sh '''
                      aws configure set aws_access_key_id "$AWS_ACCESS_KEY_ID"
                      aws configure set aws_secret_access_key "$AWS_SECRET_ACCESS_KEY"
                      aws configure set region "${AWS_REGION}"
                      aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com
                    '''
                }
            }
        }

        stage('Push Image to ECR') {
            steps {
                sh """
                docker tag ${IMAGE_REPO_NAME}:${IMAGE_TAG} ${ECR_URL}
                docker push ${ECR_URL}
                """
            }
        }

        stage('Deploy to EC2') {
            steps {
                sshagent (credentials: ['ec2-ssh']) {
                    sh """
                    ssh -o StrictHostKeyChecking=no ubuntu@${EC2_PUBLIC_IP} << 'EOF'
                    set -e
                    # Ensure AWS CLI present and login (if EC2 has no instance role)
                    aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com
                    docker pull ${ECR_URL}
                    docker stop php-rds-app || true
                    docker rm php-rds-app || true
                    docker run -d --name php-rds-app -p 80:80 --env-file /home/ubuntu/.env ${ECR_URL}
                    EOF
                    """
                }
            }
        }
    }

    post {
        success { echo '✅ Deployment successful' }
        failure { echo '❌ Deployment failed — check console output' }
    }
}
