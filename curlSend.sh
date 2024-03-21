curl -v -X POST \
    -H "Content-Type: application/json" \
    -H 'X-Requested-With: XMLHttpRequest' \
    -H 'Authorization: Token ssss' \
    -H 'Accept: application/json' \
    -H 'X-My-Custom-Header: hijames' \
    -d @content.json \
    http://localhost:8765/posts/ajax
