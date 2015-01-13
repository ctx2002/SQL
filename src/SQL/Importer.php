<?php
namespace SQL;
interface Importer
{	
    public function startTable();
    public function loadTableName();
    public function loadWidth();
    public function loadColumnNames();
    public function loadRow();
    public function endTable();
}
