variable "app_env_vars" {
    type      = string
    sensitive = true
}

resource "aws_apprunner_service" "readworth-apprunner-service" {
    service_name = "readworth-apprunner-service"
    instance_configuration {
        cpu     = "0.5 vCPU"
        memory  = 1024
    }

    source_configuration {
        authentication_configuration {
            connection_arn = "arn:aws:apprunner:ap-northeast-1:634733325676:connection/read-worth/22265587b7844f679f343ae6e9ebc23b"
#            connection_arn = aws_apprunner_connection.read-worth.arn
#            access_role_arn = aws_iam_role.apprunner_access_role.arn
        }
        code_repository {
            repository_url = "https://github.com/HayatoKudou/read_worth_api"
            source_code_version {
                type  = "BRANCH"
                value = "main"
            }
            code_configuration {
                code_configuration_values {
                    build_command = "/opt/app/docker/start-webapp.sh"
                    port          = "8000"
                    runtime       = "PHP_81"
                    start_command = "make docker/setup"
                }
                configuration_source = "API"
            }
        }
    }
}
