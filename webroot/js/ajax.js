function assign(data) {
    document.getElementById('email').innerText = data.email
    document.getElementById('method').innerText = data.method
}


function setCookie() {
    // example of setting a cookie in js
    document.cookie = "username=John Doe; expires=Thu, 18 Dec 2056 12:00:00 UTC; path=/";
}

async function get() {
    const res = await fetch('/posts/ajax?token=bbbb&method=get', {
        method: 'get',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })

    const data = await res.json();

    assign(data);

    console.log(data);
}

async function post() {
    const res = await fetch('/posts/ajax?method=post', {
        method: 'post',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': 'Token aaaa',
            'Accept': 'application/json',
            'X-CSRF-Token': csrfToken
        }
    })

    const data = await res.json();

    assign(data);

    console.log(data);
}
