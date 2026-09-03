#!/bin/bash
# Admin tests
# 1. Login
curl -s -L -c cookies.txt -b cookies.txt -d "email=admin@quantum.com&password=password" http://localhost:8000/login > /dev/null
# 2. Check Dashboard
echo "---Dashboard---"
curl -s -L -b cookies.txt http://localhost:8000/admin/dashboard | grep -i "dashboard"
# 3. List Products (CRUD test)
echo "---Products---"
curl -s -L -b cookies.txt http://localhost:8000/admin/products | grep "product"
# 4. Apriori
echo "---Apriori---"
curl -s -L -b cookies.txt http://localhost:8000/admin/apriori | grep "apriori"
