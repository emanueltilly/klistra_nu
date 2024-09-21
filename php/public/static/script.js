function loadContent(divId, loadUri) {
  if (document.getElementById(divId) == null) {
    return null;
  }

  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById(divId).innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", loadUri, true);
  xhttp.send();
}

//Load components on pageload
window.addEventListener("load", function () {
  window.fadeIn = loadContent("001_head_container", "components/001_head.php");
  window.fadeIn = loadContent(
    "002_create_container",
    "components/002_create.php"
  );
  window.fadeIn = loadContent("003_read_container", "components/003_read.php");
  window.fadeIn = loadContent(
    "004_footer_container",
    "components/004_footer.php"
  );
  window.fadeIn = loadContent(
    "005_privacy_container",
    "components/005_privacy.php"
  );

  if (document.getElementById("003_read_container") !== null) {
    loadReadPageData();
  }
});

//Form Submit Create Klister
async function createKlister() {
  // Get the value of the text field
  var textField = document.getElementsByName("text")[0];
  var textValue = textField.value;

  if (textValue.length === 0) {
    swal(
      "Empty Klister",
      "Nothing to share? Add text, and try again!",
      "warning"
    ).then(() => {
      throw new Error("Failed to create Klister, No text in textbox.");
      return false;
    });
  } else {
    // Get the value of the expiry field
    var expiryField = document.getElementById("expiry");
    var expiryValue = expiryField.value;

    // Get the value of the reqPass field
    var reqPassField = document.getElementById("reqPass");
    var reqPassValue = reqPassField.value;

    // Do something with the form values...
    var objPassProtected = reqPassValue.length > 0;

    var expiryInt = parseInt(expiryValue);

    let formJson = {
      passProtect: objPassProtected,
      pass: reqPassValue,
      expiry: expiryInt,
      pasteText: textValue,
    };

    const formJsonTransportEncrypted = await encryptJSON(formJson);

    apiPost("submit", formJsonTransportEncrypted)
      .then((response) => {
        if (response) {
          console.log("Klister created successfully:", response);
          window.location =
            window.location.protocol +
            "//" +
            window.location.host +
            "/" +
            window.klister_id;
        } else {
          console.error("Failed to create Klister.");
          swal(
            "Klister Kaos",
            "A error occured while creating the Klister.\nPlease try again.",
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("Error creating klister:", error);
        swal(
          "Klister Kaos",
          "A error occured while creating the Klister.\nPlease try again.",
          "error"
        );
      });
  }
}

function apiPost(endpoint, json) {
  return fetch(window.location.href + "api/" + endpoint, {
    method: "POST",
    body: JSON.stringify(json),
    headers: {
      "Content-type": "application/json; charset=UTF-8",
    },
  })
    .then((response) => {
      if (response.status !== 201) {
        console.error("API Call Problem. Statuscode:", response.status);
        throw new Error("API Call Problem. Statuscode:", response.status); // throw an error to trigger the catch block
      }
      return response.text(); // return a promise for the response body as a text string
    })
    .then((text) => {
      window.klister_id = text;
      console.log(window.klister_id);
      return true; // return the response data
    })
    .catch((error) => {
      console.error("API Call Error:", error);
      throw error; // re-throw the error to propagate it to the caller
    });
}

async function apiGetEntry(id, password) {
  try {
    const url =
      window.location.protocol + "//" + window.location.host + "/api/read.php";
    const data = {
      id: id,
      pass: password,
    };

    const dataEncrypted = await encryptJSON(data);

    const response = await fetch(url, {
      method: "POST",
      body: dataEncrypted,
    });
    if (!response.ok) {
      swal("Incorrect password", "Please try agian", "error").then(() => {
        location.reload();
      });
      throw new Error("Failed to fetch data");
    }

    const encryptedResponse = await response.text();
    const json = await decryptJSON(encryptedResponse);
    return json;
  } catch (error) {
    console.error("Error:", error);
    return false;
  }
}

async function apiGetEntryProtectionLevel(id) {
  let url =
    window.location.protocol +
    "//" +
    window.location.host +
    "/api/protected.php";
  const data = {
    id: id,
  };
  const response = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  // check if response status is 404 and log message to console
  if (response.status === 404) {
    console.log("Klister has expired");
    swal(
      "Expired",
      "The klister you are looking for is expired. If you expect to find a Klister here, please check the spelling of the URL.",
      "error"
    ).then(() => {
      location = window.location.protocol + "//" + window.location.host;
    });
  }

  const json = await response.json();
  return json;
}

async function getSessionKlisterId() {
  try {
    const response = await fetch(
      window.location.protocol +
        "//" +
        window.location.host +
        "/api/session.php"
    );

    if (response.status === 200) {
      const data = await response.text();
      return data;
    } else {
      return false;
    }
  } catch (error) {
    console.error(error);
    return false;
  }
}

function fadeSwap(divId, loadUri) {
  if (document.getElementById(divId) == null) {
    return null;
  }

  //Fade out
  var fadeTarget = document.getElementById(divId);
  var fadeEffect = setInterval(function () {
    if (!fadeTarget.style.opacity) {
      fadeTarget.style.opacity = 1;
    }
    if (fadeTarget.style.opacity > 0) {
      fadeTarget.style.opacity -= 0.05;
    } else {
      clearInterval(fadeEffect);
    }
  }, 10);

  //Load new content
  setTimeout(() => {
    loadContent(divId, loadUri);
  }, 200);

  //Fade In
  setTimeout(() => {
    var fadeTarget = document.getElementById(divId);
    var fadeEffect = setInterval(function () {
      if (!fadeTarget.style.opacity) {
        fadeTarget.style.opacity = 0;
      }
      if (fadeTarget.style.opacity < 1) {
        fadeTarget.style.opacity = parseFloat(fadeTarget.style.opacity) + 0.05;
      } else {
        clearInterval(fadeEffect);
      }
    }, 10);
  }, 300);
}

async function loadReadPageData() {
  //GET SESSION KLISTER ID
  $klisterID = await getSessionKlisterId();

  //GET ENTRY PASSWORD PROTECTION STATUS
  $protectionObject = await apiGetEntryProtectionLevel($klisterID);

  //ASK USER FOR PASSWORD
  if ($protectionObject.protected == true) {
    console.log("Paste is password protected");

    swal({
      title: "Enter password",
      content: {
        element: "input",
        attributes: {
          placeholder: "Password protected",
          type: "password",
        },
      },
    }).then((value) => {
      // The value variable contains the typed password

      // Get Entry Object from API
      apiGetEntry($klisterID, value)
        .then((entry) => {
          populateReadPageWithData(
            entry.text,
            entry.protected,
            entry.timeoutUnix
          );
        })
        .catch((error) => {
          console.error(error);
          // handle error here
        });
    });
  } else {
    console.log("Paste is unlocked");
    document.getElementById("hiddenIcon").style.display = "none";
    apiGetEntry($klisterID, "")
      .then((entry) => {
        populateReadPageWithData(
          entry.text,
          entry.protected,
          entry.timeoutUnix
        );
      })
      .catch((error) => {
        console.error(error);
        // handle error here
      });
  }
}

function populateReadPageWithData(text, protected, timeoutUnix) {
  //Set text
  if (text != null) {
    const textarea = document.getElementById("klisterarea");
    textarea.value = text;
    document.getElementById("klisterarea").readOnly = true;
    window.pasteText = text;
  }

  //Countdown
  window.setInterval(function () {
    var date = new Date();
    var unixTimeStamp = Math.floor(Date.now() / 1000);
    var differenceSeconds = timeoutUnix - unixTimeStamp;
    document.getElementById("countdown").innerHTML =
      "Expires in " + formatCountdownTime(differenceSeconds);

    if (differenceSeconds < 1) {
      location.reload();
    }
  }, 1000);
}

function formatCountdownTime(seconds) {
  const days = Math.floor(seconds / (24 * 60 * 60));
  const hours = Math.floor((seconds % (24 * 60 * 60)) / (60 * 60));
  const minutes = Math.floor((seconds % (60 * 60)) / 60);
  const remainingSeconds = seconds % 60;

  let result = "";
  if (days > 0) {
    result += `${days}d `;
  }
  if (hours > 0) {
    result += `${hours}h `;
  }
  if (minutes > 0) {
    result += `${minutes}m `;
  }
  if (remainingSeconds > 0) {
    result += `${remainingSeconds}s`;
  }

  return result.trim();
}

/* Github: rproenca/Clipboard.js */
window.Clipboard = (function (window, document, navigator) {
  var textArea, copy;

  function isOS() {
    return navigator.userAgent.match(/ipad|iphone/i);
  }

  function createTextArea(text) {
    textArea = document.createElement("textArea");
    textArea.value = text;
    document.body.appendChild(textArea);
  }

  function selectText() {
    var range, selection;

    if (isOS()) {
      range = document.createRange();
      range.selectNodeContents(textArea);
      selection = window.getSelection();
      selection.removeAllRanges();
      selection.addRange(range);
      textArea.setSelectionRange(0, 999999);
    } else {
      textArea.select();
    }
  }

  function copyToClipboard() {
    document.execCommand("copy");
    document.body.removeChild(textArea);
  }

  copy = function (text) {
    createTextArea(text);
    selectText();
    copyToClipboard();
  };

  return {
    copy: copy,
  };
})(window, document, navigator);

function copyToClipboard() {
  var copyText = document.getElementById("klisterarea");
  Clipboard.copy(copyText.value);
  // Alert the user that the text has been copied
  swal("Copied to clipboard", "Happy pasting!", "success");
}

// Fetch the encryption key from the backend API
async function fetchKey() {
  const existingKey = getSessionKey(); // Check if a valid key is already stored

  if (existingKey) {
    return existingKey;
  }

  const response = await fetch("/api/token", {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
    },
  });

  if (!response.ok) {
    throw new Error("Failed to fetch encryption key");
  }

  const data = await response.json();
  const key = data.key;

  setSessionKey(key); // Store key in sessionStorage with timestamp

  return key;
}

function getSessionKey() {
  const storedKeyData = sessionStorage.getItem("encryptionKey");

  if (!storedKeyData) {
    return null;
  }

  const { key, timestamp } = JSON.parse(storedKeyData);
  const now = Date.now();

  // Check if the key is older than 5 minutes (300000 ms)
  if (now - timestamp > 300000) {
    sessionStorage.removeItem("transportKey"); // Remove expired key
    return null;
  }

  return key; // Return the valid key
}

function setSessionKey(key) {
  const keyData = {
    key,
    timestamp: Date.now(),
  };

  sessionStorage.setItem("transportKey", JSON.stringify(keyData));
}

// Convert string to ArrayBuffer
function stringToArrayBuffer(str) {
  return new TextEncoder().encode(str);
}

// Convert ArrayBuffer to Base64
function arrayBufferToBase64(buffer) {
  return btoa(String.fromCharCode(...new Uint8Array(buffer)));
}

// Convert Base64 to ArrayBuffer
function base64ToArrayBuffer(base64) {
  return new Uint8Array(
    atob(base64)
      .split("")
      .map((char) => char.charCodeAt(0))
  );
}

// Transport Encrypt (Obfuscation during transport only)
async function encryptJSON(jsonData) {
  const keyString = await fetchKey(); // Get key from API
  const key = await crypto.subtle.importKey(
    "raw",
    new TextEncoder().encode(keyString), // Use the fetched key
    { name: "AES-CBC" },
    false,
    ["encrypt"]
  );

  const iv = new Uint8Array([
    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16,
  ]); // Static IV
  const jsonStr = JSON.stringify(jsonData);
  const encoded = stringToArrayBuffer(jsonStr);

  const encryptedData = await crypto.subtle.encrypt(
    { name: "AES-CBC", iv },
    key,
    encoded
  );

  return arrayBufferToBase64(encryptedData);
}

// Transport Decrypt (Obfuscation during transport only)
async function decryptJSON(encryptedBase64) {
  const keyString = await fetchKey(); // Get key from API
  const key = await crypto.subtle.importKey(
    "raw",
    new TextEncoder().encode(keyString), // Use the fetched key
    { name: "AES-CBC" },
    false,
    ["decrypt"]
  );

  const iv = new Uint8Array([
    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16,
  ]); // Static IV
  const encryptedArrayBuffer = base64ToArrayBuffer(encryptedBase64);

  const decryptedData = await crypto.subtle.decrypt(
    { name: "AES-CBC", iv },
    key,
    encryptedArrayBuffer
  );

  const decryptedString = new TextDecoder().decode(decryptedData);
  return JSON.parse(decryptedString);
}
