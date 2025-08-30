# Overview

This is a PHP PIX payment API fully configured and ready for deployment. The API integrates with MercadoPago's payment processing services using real credentials and connects to a PostgreSQL database. All endpoints are tested and working correctly.

# User Preferences

Preferred communication style: Simple, everyday language.

# System Architecture

## Core Framework
- **Language**: PHP with Composer dependency management
- **Namespace Structure**: PSR-4 autoloading with `App\` namespace mapped to `src/` directory
- **Architecture Pattern**: Likely follows a simple MVC or service-oriented architecture given the namespace setup

## Payment Processing
- **Payment Gateway**: MercadoPago integration using the official `mercadopago/dx-php` SDK (version ^2.5)
- **Integration Approach**: Server-side payment processing with PHP backend handling MercadoPago API calls
- **SDK Choice**: Uses the official MercadoPago Developer Experience PHP library for robust payment handling

## Project Structure
- **Source Code**: Organized under `src/` directory with PSR-4 autoloading
- **Dependency Management**: Composer-based with minimal external dependencies
- **Code Organization**: Namespace-based structure allowing for scalable application architecture

# API Configuration (PRODUCTION READY)

## Configured Credentials
- **MercadoPago Access Token**: APP_USR-8069564433357692-083003-24d6800f4e506c0095dc3b12e976dd63-617282063
- **MercadoPago Public Key**: APP_USR-bc00cde5-0fec-4e77-8729-0eb5670df2c3
- **PostgreSQL Database**: postgresql://bqrdjune:nifvcxmmdsapgucosrrq@alpha.mkdb.sh:5432/zrtlmoxa
- **Webhook URL**: centralizando.com

## API Endpoints (TESTED & WORKING)
1. **POST /criar_pagamento.php** - Creates PIX payments
2. **GET /consultar_status.php?id={payment_id}** - Checks payment status
3. **POST /webhook.php** - Receives MercadoPago notifications

## External Dependencies
- **MercadoPago SDK**: `mercadopago/dx-php` ^2.5 (installed and configured)
- **PostgreSQL**: Database tables created and ready
- **PHP 8.2**: Installed with all required extensions

## Payment Rules
- Fixed values: R$ 20, 40, 60, 80, 100
- Custom values: Minimum R$ 100
- PIX payment method only
- Real-time webhook notifications