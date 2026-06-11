SHELL := /usr/bin/env bash
.PHONY: up down reset logs shell wp seed smoke status

COMPOSE := docker compose
WP := $(COMPOSE) run --rm wpcli wp

up:
	$(COMPOSE) up -d
	@echo "→ Waiting for WordPress to respond..."
	@for i in $$(seq 1 60); do \
	  if curl -sf -o /dev/null http://localhost:8080; then echo "✓ WP reachable"; exit 0; fi; \
	  sleep 2; \
	done; echo "✗ WP did not respond in 120s"; exit 1

down:
	$(COMPOSE) down

reset:
	$(COMPOSE) down -v
	$(MAKE) up
	$(MAKE) seed

logs:
	$(COMPOSE) logs -f

status:
	$(COMPOSE) ps

shell:
	$(COMPOSE) exec wordpress bash

wp:
	$(WP) $(ARGS)

seed:
	bash bin/setup.sh

smoke:
	bash bin/smoke.sh
