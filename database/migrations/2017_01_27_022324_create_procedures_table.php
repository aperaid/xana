<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::unprepared('CREATE PROCEDURE edit_sjkembali( IN `p_q` INT, IN `p_purchase` INT, IN `p_sjkem` VARCHAR(20) ) 
BEGIN  
DECLARE var_q int DEFAULT 0;
DECLARE var_qtertanda int DEFAULT 0;
DECLARE var_isisjkem int DEFAULT 0;
DECLARE var_isisjkir int DEFAULT 0;

SET var_q = p_q;

WHILE (var_q > 0) DO

SELECT MIN(isisjkembali.IsiSJKem) INTO var_isisjkem FROM isisjkembali WHERE isisjkembali.QTertanda = 0 AND isisjkembali.Purchase = p_purchase AND SJKem=p_sjkem;

SELECT MIN(isisjkirim.IsiSJKir) INTO var_isisjkir FROM isisjkirim WHERE isisjkirim.QSisaKemInsert > 0 AND isisjkirim.Purchase = p_purchase;

SELECT isisjkirim.QTertanda into var_qtertanda FROM isisjkirim WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

IF var_q > var_qtertanda THEN

UPDATE isisjkirim SET isisjkirim.QSisaKemInsert = isisjkirim.QSisaKemInsert - var_qtertanda WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

UPDATE isisjkembali SET isisjkembali.QTertanda = var_qtertanda WHERE isisjkembali.IsiSJKem = var_isisjkem AND isisjkembali.Purchase = p_purchase;

SELECT var_q - isisjkirim.QTertanda into var_q FROM isisjkirim WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

ELSE

UPDATE isisjkirim SET isisjkirim.QSisaKemInsert = isisjkirim.QSisaKem  - var_q WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

UPDATE isisjkembali SET isisjkembali.QTertanda = var_q WHERE isisjkembali.IsiSJKem = var_isisjkem AND isisjkembali.Purchase = p_purchase;

SELECT var_q - var_q into var_q FROM isisjkirim WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

END IF;

END WHILE; 
END');

      DB::unprepared('CREATE PROCEDURE edit_sjkembaliquantity( IN `p_q` INT, IN `p_purchase` INT, IN `p_sjkem` VARCHAR(20), IN `p_isisjkir` INT ) 
BEGIN  
DECLARE var_q int DEFAULT 0;
DECLARE var_qtertanda int DEFAULT 0;
DECLARE var_qterima int DEFAULT 0;
DECLARE var_isisjkem int DEFAULT 0;
DECLARE var_isisjkir int DEFAULT 0;

SET var_q = p_q;

WHILE (var_q > 0) DO

SELECT MIN(isisjkembali.IsiSJKem) INTO var_isisjkem FROM isisjkembali WHERE isisjkembali.QTertanda != isisjkembali.QTerima AND isisjkembali.Purchase = p_purchase AND SJKem=p_sjkem;

SELECT MIN(isisjkirim.IsiSJKir) INTO var_isisjkir FROM isisjkirim WHERE isisjkirim.QSisaKem > 0 AND isisjkirim.Purchase = p_purchase;

SELECT isisjkembali.QTertanda into var_qtertanda FROM isisjkembali WHERE isisjkembali.IsiSJKem = var_isisjkem;

SELECT isisjkembali.QTerima into var_qterima FROM isisjkembali WHERE isisjkembali.IsiSJKem = var_isisjkem;

IF var_q > var_qtertanda THEN

UPDATE isisjkirim SET isisjkirim.QSisaKem = isisjkirim.QSisaKem + var_qterima - var_qtertanda WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

UPDATE isisjkembali SET isisjkembali.QTerima = var_qtertanda WHERE isisjkembali.IsiSJKem = var_isisjkem AND isisjkembali.Purchase = p_purchase;

SELECT var_q - isisjkembali.QTertanda into var_q FROM isisjkembali WHERE isisjkembali.IsiSJKem = var_isisjkem;

ELSE

UPDATE isisjkirim SET isisjkirim.QSisaKem = isisjkirim.QSisaKem + var_qterima - var_q WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

UPDATE isisjkembali SET isisjkembali.QTerima = var_q WHERE isisjkembali.IsiSJKem = var_isisjkem AND isisjkembali.Purchase = p_purchase;

SELECT var_q - var_q into var_q FROM isisjkembali WHERE isisjkembali.IsiSJKem = var_isisjkem;

END IF;

END WHILE;
END');

      DB::unprepared('CREATE PROCEDURE insert_claim( IN `p_q` INT, IN `p_purchase` INT, IN `p_periode` INT, IN `p_claim` INT ) 
BEGIN  
DECLARE var_q int DEFAULT 0;
DECLARE var_isisjkir int DEFAULT 0;

SET var_q = p_q;

WHILE (var_q > 0) DO

SELECT MIN(isisjkirim.IsiSJKir) INTO var_isisjkir FROM isisjkirim WHERE isisjkirim.QSisaKem > 0 AND isisjkirim.Purchase = p_purchase;

SELECT var_q - isisjkirim.QSisaKem INTO var_q FROM isisjkirim WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

IF var_q < 0 THEN
UPDATE isisjkirim SET isisjkirim.QSisaKemInsert = ABS(var_q), isisjkirim.QSisaKem = ABS(var_q) WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;
UPDATE periode SET periode.Quantity = ABS(var_q) WHERE periode.Periode = p_periode AND periode.IsiSJKir = var_isisjkir AND periode.Purchase = p_purchase AND (periode.Deletes = "Sewa" OR periode.Deletes = "Extend");
UPDATE periode SET periode.Quantity = periode.Quantity + var_q, periode.Claim = p_claim WHERE periode.Periode = p_periode AND periode.IsiSJKir = var_isisjkir AND periode.Purchase = p_purchase AND periode.Deletes = "Claim";
ELSE
UPDATE isisjkirim SET isisjkirim.QSisaKemInsert = 0, isisjkirim.QSisaKem = 0 WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;
UPDATE periode SET periode.Quantity = 0 WHERE periode.Periode = p_periode AND periode.IsiSJKir = var_isisjkir AND periode.Purchase = p_purchase AND (periode.Deletes = "Sewa" OR periode.Deletes = "Extend");
UPDATE periode SET periode.Claim = p_claim WHERE periode.Periode = p_periode AND periode.IsiSJKir = var_isisjkir AND periode.Purchase = p_purchase AND periode.Deletes = "Claim" AND periode.Claim IS NULL;
END IF;
END WHILE;
END');

      DB::unprepared('CREATE PROCEDURE insert_claim2() 
BEGIN  
DELETE FROM periode WHERE periode.Deletes="Claim" AND periode.Claim IS NULL AND EXISTS (SELECT * FROM isisjkirim WHERE isisjkirim.IsiSJKir = periode.IsiSJKir AND isisjkirim.QTertanda = periode.Quantity AND isisjkirim.Purchase = periode.Purchase AND periode.Deletes="Claim");
DELETE FROM periode WHERE periode.Deletes = "Claim" AND periode.Quantity = 0;
ALTER TABLE periode AUTO_INCREMENT = 1;

DELETE FROM transaksiclaim WHERE transaksiclaim.QClaim = 0;
ALTER TABLE transaksiclaim AUTO_INCREMENT = 1;
END');

      DB::unprepared('CREATE PROCEDURE insert_sjkembali( IN `p_q` INT, IN `p_purchase` INT, IN `p_periode` INT, IN `p_sjkem` VARCHAR(20) ) 
BEGIN  
DECLARE var_q int DEFAULT 0;
DECLARE var_isisjkir int DEFAULT 0;

SET var_q = p_q;

WHILE (var_q > 0) DO

SELECT MIN(isisjkirim.IsiSJKir) INTO var_isisjkir FROM isisjkirim WHERE isisjkirim.QSisaKemInsert > 0 AND isisjkirim.Purchase = p_purchase;

SELECT var_q - isisjkirim.QSisaKemInsert INTO var_q FROM isisjkirim WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;

IF var_q < 0 THEN
UPDATE isisjkirim SET isisjkirim.QSisaKemInsert = ABS(var_q) WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;
UPDATE periode SET periode.Quantity = ABS(var_q) WHERE periode.Periode = p_periode AND periode.IsiSJKir = var_isisjkir AND periode.Purchase = p_purchase AND (periode.Deletes = "Sewa" OR periode.Deletes = "Extend");
UPDATE periode SET periode.Quantity = periode.Quantity + var_q WHERE  periode.Periode = p_periode AND periode.IsiSJKir = var_isisjkir AND periode.Purchase = p_purchase AND periode.Deletes = "Kembali" AND periode.SJKem = p_sjkem;
ELSE
UPDATE isisjkirim SET isisjkirim.QSisaKemInsert = 0 WHERE isisjkirim.IsiSJKir = var_isisjkir AND isisjkirim.Purchase = p_purchase;
UPDATE periode SET periode.Quantity = 0 WHERE periode.Periode = p_periode AND periode.IsiSJKir = var_isisjkir AND periode.Purchase = p_purchase AND (periode.Deletes = "Sewa" OR periode.Deletes = "Extend");
END IF;
END WHILE;
END');

      DB::unprepared('CREATE PROCEDURE insert_sjkembali2() 
BEGIN  
DELETE FROM periode WHERE periode.Deletes="Kembali" AND EXISTS (SELECT * FROM isisjkirim WHERE isisjkirim.IsiSJKir = periode.IsiSJKir AND isisjkirim.QTertanda = periode.Quantity AND isisjkirim.Purchase = periode.Purchase AND periode.Deletes="Kembali" AND isisjkirim.QSisaKemInsert=isisjkirim.QSisaKem);
DELETE FROM periode WHERE periode.Deletes = "Kembali" AND periode.Quantity = 0;
ALTER TABLE periode AUTO_INCREMENT = 1;

DELETE FROM isisjkembali WHERE isisjkembali.QTertanda = 0;
ALTER TABLE isisjkembali AUTO_INCREMENT = 1;
END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::unprepared('DROP PROCEDURE IF EXISTS edit_sjkembali');
      DB::unprepared('DROP PROCEDURE IF EXISTS edit_sjkembaliquantity');
      DB::unprepared('DROP PROCEDURE IF EXISTS insert_claim');
      DB::unprepared('DROP PROCEDURE IF EXISTS insert_claim2');
      DB::unprepared('DROP PROCEDURE IF EXISTS insert_sjkembali');
      DB::unprepared('DROP PROCEDURE IF EXISTS insert_sjkembali2');
    }
}
