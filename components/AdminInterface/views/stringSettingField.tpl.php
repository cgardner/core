<?php
echo $this->fh->labelFor($setting['title'], $setting['name']);
echo $this->fh->textFieldTag($setting['name'], $setting['value']);
?>