TERRAFORM=../bin/terraform

.PHONY: setup validate plan apply

setup: $(TERRAFORM)
	aws-vault exec gym -- $(TERRAFORM) init

validate:
	$(TERRAFORM) validate

plan:
	aws-vault exec gym -- $(TERRAFORM) plan -var "app_env_vars=$(shell base64 app-env-vars.txt)"

apply:
	aws-vault exec gym -- $(TERRAFORM) apply -var "app_env_vars=$(shell base64 app-env-vars.txt)"


# no PHONY
$(TERRAFORM):
	$(MAKE) -C ../ terraform/install
