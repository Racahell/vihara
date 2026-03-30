<h2>Selamat datang, {{ auth()->user()->nama }}</h2>
<p>Role: {{ auth()->user()->peran }}</p>

<a href="/logout">Logout</a>