<?php
session_start(); ?>

<div class="create_container_master">

<h4 id="countdown">Expires in </h4>
<span class="material-symbols-outlined" style="float:right; margin-top:-45px; padding: 10px;" id="hiddenIcon">visibility_lock</span>

<form class="createForm" autocomplete="off"">
    
    <textarea name="text" id="klisterarea" cols="100" rows="25">
        
    </textarea>

    <div class="createFormSubmitWrapper">
        <input type="button" value="Copy to Clipboard" onclick="copyToClipboard()"><br>
    </div>

</form>

</div>


