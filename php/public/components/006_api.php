<?php
session_start(); ?>
<div class="docs_container_master">
<h2>API Documentation</h2>
<p>API Version 1.0 - Updated 2023-05-05</p>

<h4>REST API</h4>

<p>For DIY users looking to integrate the Klistra.nu API into their own frontends, applications, or automations, the service offers a simple yet secure way to store and access text strings.</p>

<p>By making use of the API's straightforward endpoints, you can easily send and retrieve text data. The API also allows for specifying the desired expiration time of the data, as well as enabling password protection for added security.</p>

<br>
<h4>SUBMIT</h4>
  <p>
    Endpoint: <code>https://klistra.nu/api/submit</code>
    <br>
    Required method: <code>POST</code>
    <br>
    Required body: <code>JSON Object</code>
  </p>
  <h4>JSON Object Keys and Values</h4>
  <ul>
    <li><code>expiry</code>: Required integer value between 60 and 604800 seconds (1 minute to 7 days) representing the time after which the data will be deleted automatically.</li>
    <li><code>passProtect</code>: Required boolean value indicating whether password protection is required or not.</li>
    <li><code>pass</code>: Required string value representing the password to access the data. Leave blank if <code>passProtect</code> is false.</li>
    <li><code>pasteText</code>: Required string value representing the main data to be stored.</li>
  </ul>
  <p>
    A successful request should result in a <code>201 Created</code> status and <code>Content-Type: text/plain</code> with the created paste ID.
  </p>
  <h4>Example request:</h4>
  <p><code>{
    "passProtect": true,
    "pass": "mypassword",
    "expiry": 3600,
    "pasteText": "User Text Data"
}</code></p>

<br>
<h4>READ</h4>
  <p>
    Endpoint: <code>https://klistra.nu/api/read</code>
    <br>
    Required method: <code>POST</code>
    <br>
    Required body: <code>JSON Object</code>
  </p>
  <h4>JSON Object Keys and Values</h4>
  <ul>
        <li><code>id</code>: Required string value with the requested ID of the data you wish to read.</li>
        <li><code>pass</code>: Required string value representing the password to access the data. Leave blank if not password protected.</li>
  </ul>
  <p>
    A successful request should result in a <code>200 OK</code> status and <code>Content-Type: application/json</code> with the requested data.
  </p>
  <h4>Example request:</h4>
  <p><code>{
    "id": "hawk34",
    "pass": "mypassword"
}</code></p>
<h4>Example response:</h4>
  <p><code>{
    "id":"hawk34",
    "timeoutUnix":1683288219,
    "passwordProtected":true,
    "text":"test"
}</code>
</p>

<br>
<h4>UPDATE</h4>
  <p>
    Endpoint: <code>https://klistra.nu/api/update</code>
    <br>
    Required method: <code>POST</code>
    <br>
    Required body: <code>JSON Object</code>
  </p>
  <h4>JSON Object Keys and Values</h4>
  <ul>
        <li><code>id</code>: Required string value representing the ID of the existing data you wish to update.</li>
        <li><code>pass</code>: Required string value representing the password to access the data. This must be the correct current password of the data you wish to update.</li>
        <li><code>pasteText</code>: Required string value representing the updated text data.</li>
  </ul>
  <p>
    A successful request should result in a <code>200 OK</code> status and <code>Content-Type: text/plain</code> with the confirmation of the update.
  </p>
  <h4>Example request:</h4>
  <p><code>{
    "id": "hawk34",
    "pass": "mypassword",
    "pasteText": "Updated Text Data"
  }</code></p>
<h4>Example response:</h4>
  <p><code>{
    "id":"hawk34",
    "timeoutUnix":1683288219,
    "passwordProtected":true,
    "text":"test"
}</code>
</p>

<p>
Please note that updating an entry will not change its expiration time or password. It only updates the text data stored under that entry. The password provided in the request must match the current password of the entry for the update to be successful. The updated entry will maintain the same paste ID as provided in the request.
  <br>
</p>

<br>
<h4>GET PROTECTED STATUS</h4>
<p>Use this to get a true/false statement for the password protection status of a specific id. Useful for knowing whether or not to prompt user for password input.</p>
  <p>
    Endpoint: <code>https://klistra.nu/api/protected</code>
    <br>
    Required method: <code>POST</code>
    <br>
    Required body: <code>JSON Object</code>
  </p>
  <h4>JSON Object Keys and Values</h4>
  <ul>
        <li><code>id</code>: Required string value with the requested ID of the data you wish to get protection level of.</li>
  </ul>
  <p>
    A successful request should result in a <code>200 OK</code> status and <code>Content-Type: application/json</code> with the requested data.
  </p>
  <h4>Example request:</h4>
  <p><code>{
    "id": "hawk34"
}</code></p>
<h4>Example response:</h4>
  <p><code>{"passwordProtected":true}</code>
</p>

<br>



</div>