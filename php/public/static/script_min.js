function loadContent(e, t) {
  if (null == document.getElementById(e)) return null;
  var o = new XMLHttpRequest();
  (o.onreadystatechange = function () {
    4 == this.readyState &&
      200 == this.status &&
      (document.getElementById(e).innerHTML = this.responseText);
  }),
    o.open("GET", t, !0),
    o.send();
}
async function createKlister() {
  var e = document.getElementsByName("text")[0].value;
  if (0 === e.length)
    swal(
      "Empty Klister",
      "Nothing to share? Add text, and try again!",
      "warning"
    ).then(() => {
      throw new Error("Failed to create Klister, No text in textbox.");
    });
  else {
    var t = document.getElementById("expiry").value,
      o = document.getElementById("reqPass").value;
    let n = {
      passProtect: o.length > 0,
      pass: o,
      expiry: parseInt(t),
      pasteText: e,
    };
    apiPost("submit", await encryptJSON(n))
      .then((e) => {
        e
          ? (console.log("Klister created successfully:", e),
            (window.location =
              window.location.protocol +
              "//" +
              window.location.host +
              "/" +
              window.klister_id))
          : (console.error("Failed to create Klister."),
            swal(
              "Klister Kaos",
              "A error occured while creating the Klister.\nPlease try again.",
              "error"
            ));
      })
      .catch((e) => {
        console.error("Error creating klister:", e),
          swal(
            "Klister Kaos",
            "A error occured while creating the Klister.\nPlease try again.",
            "error"
          );
      });
  }
}
function apiPost(e, t) {
  return fetch(window.location.href + "api/" + e, {
    method: "POST",
    body: JSON.stringify(t),
    headers: { "Content-type": "application/json; charset=UTF-8" },
  })
    .then((e) => {
      if (201 !== e.status)
        throw (
          (console.error("API Call Problem. Statuscode:", e.status),
          new Error("API Call Problem. Statuscode:", e.status))
        );
      return e.text();
    })
    .then((e) => ((window.klister_id = e), console.log(window.klister_id), !0))
    .catch((e) => {
      throw (console.error("API Call Error:", e), e);
    });
}
async function apiGetEntry(e, t) {
  try {
    const o =
        window.location.protocol +
        "//" +
        window.location.host +
        "/api/read.php",
      n = { id: e, pass: t },
      a = await encryptJSON(n),
      r = await fetch(o, { method: "POST", body: a });
    if (!r.ok)
      throw (
        (swal("Incorrect password", "Please try agian", "error").then(() => {
          location.reload();
        }),
        new Error("Failed to fetch data"))
      );
    const i = await r.text();
    return await decryptJSON(i);
  } catch (e) {
    return console.error("Error:", e), !1;
  }
}
async function apiGetEntryProtectionLevel(e) {
  let t =
    window.location.protocol +
    "//" +
    window.location.host +
    "/api/protected.php";
  const o = { id: e },
    n = await fetch(t, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(o),
    });
  404 === n.status &&
    (console.log("Klister has expired"),
    swal(
      "Expired",
      "The klister you are looking for is expired. If you expect to find a Klister here, please check the spelling of the URL.",
      "error"
    ).then(() => {
      location = window.location.protocol + "//" + window.location.host;
    }));
  return await n.json();
}
async function getSessionKlisterId() {
  try {
    const e = await fetch(
      window.location.protocol +
        "//" +
        window.location.host +
        "/api/session.php"
    );
    if (200 === e.status) {
      return await e.text();
    }
    return !1;
  } catch (e) {
    return console.error(e), !1;
  }
}
function fadeSwap(e, t) {
  if (null == document.getElementById(e)) return null;
  var o = document.getElementById(e),
    n = setInterval(function () {
      o.style.opacity || (o.style.opacity = 1),
        o.style.opacity > 0 ? (o.style.opacity -= 0.05) : clearInterval(n);
    }, 10);
  setTimeout(() => {
    loadContent(e, t);
  }, 200),
    setTimeout(() => {
      var t = document.getElementById(e),
        o = setInterval(function () {
          t.style.opacity || (t.style.opacity = 0),
            t.style.opacity < 1
              ? (t.style.opacity = parseFloat(t.style.opacity) + 0.05)
              : clearInterval(o);
        }, 10);
    }, 300);
}
async function loadReadPageData() {
  ($klisterID = await getSessionKlisterId()),
    ($protectionObject = await apiGetEntryProtectionLevel($klisterID)),
    1 == $protectionObject.passwordProtected
      ? (console.log("Paste is password protected"),
        swal({
          title: "Enter password",
          content: {
            element: "input",
            attributes: { placeholder: "Password protected", type: "password" },
          },
        }).then((e) => {
          apiGetEntry($klisterID, e)
            .then((e) => {
              populateReadPageWithData(
                e.text,
                e.passwordProtected,
                e.timeoutUnix
              );
            })
            .catch((e) => {
              console.error(e);
            });
        }))
      : (console.log("Paste is unlocked"),
        (document.getElementById("hiddenIcon").style.display = "none"),
        apiGetEntry($klisterID, "")
          .then((e) => {
            populateReadPageWithData(
              e.text,
              e.passwordProtected,
              e.timeoutUnix
            );
          })
          .catch((e) => {
            console.error(e);
          }));
}
function populateReadPageWithData(e, t, o) {
  if (null != e) {
    (document.getElementById("klisterarea").value = e),
      (document.getElementById("klisterarea").readOnly = !0),
      (window.pasteText = e);
  }
  window.setInterval(function () {
    new Date();
    var e = Math.floor(Date.now() / 1e3),
      t = o - e;
    (document.getElementById("countdown").innerHTML =
      "Expires in " + formatCountdownTime(t)),
      t < 1 && location.reload();
  }, 1e3);
}
function formatCountdownTime(e) {
  const t = Math.floor(e / 86400),
    o = Math.floor((e % 86400) / 3600),
    n = Math.floor((e % 3600) / 60),
    a = e % 60;
  let r = "";
  return (
    t > 0 && (r += `${t}d `),
    o > 0 && (r += `${o}h `),
    n > 0 && (r += `${n}m `),
    a > 0 && (r += `${a}s`),
    r.trim()
  );
}
function copyToClipboard() {
  var e = document.getElementById("klisterarea");
  Clipboard.copy(e.value),
    swal("Copied to clipboard", "Happy pasting!", "success");
}
async function fetchKey() {
  const e = getSessionKey();
  if (e) return e;
  const t = await fetch("/api/token", {
    method: "GET",
    headers: { "Content-Type": "application/json" },
  });
  if (!t.ok) throw new Error("Failed to fetch encryption key");
  const o = (await t.json()).key;
  return setSessionKey(o), o;
}
function getSessionKey() {
  const e = sessionStorage.getItem("encryptionKey");
  if (!e) return null;
  const { key: t, timestamp: o } = JSON.parse(e);
  return Date.now() - o > 3e5
    ? (sessionStorage.removeItem("transportKey"), null)
    : t;
}
function setSessionKey(e) {
  const t = { key: e, timestamp: Date.now() };
  sessionStorage.setItem("transportKey", JSON.stringify(t));
}
function stringToArrayBuffer(e) {
  return new TextEncoder().encode(e);
}
function arrayBufferToBase64(e) {
  return btoa(String.fromCharCode(...new Uint8Array(e)));
}
function base64ToArrayBuffer(e) {
  return new Uint8Array(
    atob(e)
      .split("")
      .map((e) => e.charCodeAt(0))
  );
}
async function encryptJSON(e) {
  const t = await fetchKey(),
    o = await crypto.subtle.importKey(
      "raw",
      new TextEncoder().encode(t),
      { name: "AES-CBC" },
      !1,
      ["encrypt"]
    ),
    n = new Uint8Array([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]),
    a = stringToArrayBuffer(JSON.stringify(e));
  return arrayBufferToBase64(
    await crypto.subtle.encrypt({ name: "AES-CBC", iv: n }, o, a)
  );
}
async function decryptJSON(e) {
  const t = await fetchKey(),
    o = await crypto.subtle.importKey(
      "raw",
      new TextEncoder().encode(t),
      { name: "AES-CBC" },
      !1,
      ["decrypt"]
    ),
    n = new Uint8Array([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]),
    a = base64ToArrayBuffer(e),
    r = await crypto.subtle.decrypt({ name: "AES-CBC", iv: n }, o, a),
    i = new TextDecoder().decode(r);
  return JSON.parse(i);
}
window.addEventListener("load", function () {
  (window.fadeIn = loadContent(
    "001_head_container",
    "components/001_head.php"
  )),
    (window.fadeIn = loadContent(
      "002_create_container",
      "components/002_create.php"
    )),
    (window.fadeIn = loadContent(
      "003_read_container",
      "components/003_read.php"
    )),
    (window.fadeIn = loadContent(
      "004_footer_container",
      "components/004_footer.php"
    )),
    (window.fadeIn = loadContent(
      "005_privacy_container",
      "components/005_privacy.php"
    )),
    (window.fadeIn = loadContent(
      "006_api_container",
      "components/006_api.php"
    )),
    (window.fadeIn = loadContent(
      "007_stats_container",
      "components/007_stats.php"
    )),
    null !== document.getElementById("003_read_container") &&
      loadReadPageData();
}),
  (window.Clipboard = (function (e, t, o) {
    var n;
    function a() {
      var a, r;
      o.userAgent.match(/ipad|iphone/i)
        ? ((a = t.createRange()).selectNodeContents(n),
          (r = e.getSelection()).removeAllRanges(),
          r.addRange(a),
          n.setSelectionRange(0, 999999))
        : n.select();
    }
    return {
      copy: function (e) {
        !(function (e) {
          ((n = t.createElement("textArea")).value = e), t.body.appendChild(n);
        })(e),
          a(),
          t.execCommand("copy"),
          t.body.removeChild(n);
      },
    };
  })(window, document, navigator));
