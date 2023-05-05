<?php
session_start(); ?>
<div class="create_container_master">
<h4>Create new text share</h4>
<form class="createForm" autocomplete="off" onsubmit="createKlister(); return false;">
    
    <textarea name="text" cols="100" rows="25"></textarea>

    <div class="createFormPropertyRow">
        <div class="createFormPropertyRowLeft">
            <label for="expiry">Klister expiry:</label>
        </div>
        <div class="createFormPropertyRowRight">
            <select name="expiry" id="expiry">
                <option value="1800">30 min</option>
                <option value="3600">1 hour</option>
                <option value="21600">6 hours</option>
                <option value="43200">12 hours</option>
                <option value="86400">1 day</option>
                <option value="259200">3 days</option>
                <option value="604800">7 days</option>
            </select>
        </div>
    </div>
    <div class="createFormPropertyRow">
        <div class="createFormPropertyRowLeft">
            <label for="reqPass">Password protect:</label>
        </div>
        <div class="createFormPropertyRowRight">
            <input id="reqPass" name="reqPass">
        </div>
    </div>
    <div class="createFormSubmitWrapper">
        <input type="submit" value="Create Klister"><br>
    </div>
    
</form>
</div>