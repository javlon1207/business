const BASE_PREFIX = location.pathname.startsWith("/public/") || location.pathname === "/public" ? "/public" : "";
const API_BASE = `${BASE_PREFIX}/api/index.php`;

function setToken(token){ localStorage.setItem("token", token); }
function getToken(){ return localStorage.getItem("token"); }
function clearToken(){ localStorage.removeItem("token"); localStorage.removeItem("user"); }

function setUser(u){ localStorage.setItem("user", JSON.stringify(u)); }
function getUser(){ try { return JSON.parse(localStorage.getItem("user")||"null"); } catch(e){ return null; } }

async function api(path, {method="GET", body=null} = {}) {
  const headers = { "Content-Type": "application/json" };
  const t = getToken();
  if (t) headers["Authorization"] = "Bearer " + t;

  // Works with or without mod_rewrite: index.php routes by ?r=/path
  const url = API_BASE + "?r=" + encodeURIComponent(path);

  const res = await fetch(url, { method, headers, body: body ? JSON.stringify(body) : null });

  const text = await res.text();
  let data;
  try { data = JSON.parse(text); } catch(e){ data = { raw: text }; }
  if (!res.ok) throw data;
  return data;
}

function requireLogin(role=null){
  const u = getUser();
  if (!u || !getToken()) { window.location.href = `${BASE_PREFIX}/login.html`; return; }
  if (role && u.role !== role) { window.location.href = `${BASE_PREFIX}/login.html`; return; }
}

function logout(){
  clearToken();
  window.location.href = `${BASE_PREFIX}/login.html`;
}
