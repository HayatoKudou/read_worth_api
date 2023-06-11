TERRAFORM = ./bin/terraform
TERRAFORM_VERSION = 1.2.3
TERRAFORM_PLATFORM = darwin_amd64

.PHONY: setup lint fmt terraform/install

setup: terraform/install

lint:
	$(TERRAFORM) fmt -check -diff -recursive

fmt:
	$(TERRAFORM) fmt -recursive

terraform/install:
	curl -sSfL https://releases.hashicorp.com/terraform/$(TERRAFORM_VERSION)/terraform_$(TERRAFORM_VERSION)_$(TERRAFORM_PLATFORM).zip -o terraform.zip
	unzip terraform.zip && rm terraform.zip && mv terraform ./bin/
	./bin/terraform -version
