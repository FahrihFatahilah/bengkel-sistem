pipeline {
    agent any

    stages {

        stage('Checkout') {
            steps {
                git branch: 'main',
                    credentialsId: 'git-credentials',
                    url: 'https://github.com/username/bengkel2.git'
            }
        }

        stage('Deploy') {
            steps {
                sshagent(['deploy-server-ssh']) {
                    // Copy .env dari Jenkins credentials ke server
                    withCredentials([file(credentialsId: 'bengkel-env', variable: 'ENV_FILE')]) {
                        sh """
                            scp -o StrictHostKeyChecking=no \$ENV_FILE root@your-server-ip:/opt/bengkel/.env
                            ssh -o StrictHostKeyChecking=no root@your-server-ip '
                                cd /opt/bengkel &&
                                git pull origin main &&
                                chmod +x deploy.sh &&
                                ./deploy.sh
                            '
                        """
                    }
                }
            }
        }

    }

    post {
        success {
            echo "✅ Deploy berhasil - Build #${BUILD_NUMBER}"
        }
        failure {
            echo "❌ Deploy gagal - Build #${BUILD_NUMBER}"
        }
    }
}
