<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE DEFINER =`root`@`localhost` PROCEDURE `expose_nametags_family`(myfamily_id INT)
            BEGIN
                SET sql_mode='';
                DELETE FROM nametag_expo WHERE yearattending_id IN (SELECT ya.id FROM yearsattending ya, campers c WHERE ya.camper_id=c.id AND c.family_id=myfamily_id);
                INSERT INTO nametag_expo (yearattending_id, pronoun, name, surname, line1, line2, line3, line4, font, parent, icon, created_at)
                    SELECT ya.id,
                        IF(SUBSTR(ya.nametag, 1, 1) = \"2\", p.name, \"\") pronoun,
                        IF(SUBSTR(ya.nametag, 2, 1) = \"2\", CONCAT(c.firstname, ' ', c.lastname), c.firstname) name,
		                IF(SUBSTR(ya.nametag, 2, 1) = \"1\", c.lastname, \"\") surname,
		                getnametag (ya.id, 1) line1, getnametag (ya.id, 2) line2,
		                getnametag (ya.id, 3) line3, getnametag (ya.id, 4) line4,
                        CASE WHEN SUBSTR(ya.nametag, 9, 1) = \"2\" THEN \"Indie Flower\"
                            WHEN SUBSTR(ya.nametag, 9, 1) = \"3\" THEN \"Fredericka the Great\"
                            WHEN SUBSTR(ya.nametag, 9, 1) = \"4\" THEN \"Mystery Quest\"
                            WHEN SUBSTR(ya.nametag, 9, 1) = \"5\" THEN \"Great Vibes\"
                            WHEN SUBSTR(ya.nametag, 9, 1) = \"2\" THEN \"Bangers\"
                            WHEN SUBSTR(ya.nametag, 9, 1) = \"2\" THEN \"Comic Sans MS\"
                            ELSE \"Jost\" END font,
                        IF(getrealage (c.birthdate, ya.year_id) < 18, IFNULL(pcod.parentname, \"SPONSOR NEEDED\"), \"\") parent,
                        IF(getrealage (c.birthdate, ya.year_id) < 18, IFNULL(pcod.icon, \"shield-exclamation\"), \"\") icon, NOW()
                    FROM (yearsattending ya, campers c, pronouns p)
                    LEFT OUTER JOIN (
                        SELECT pco.child_yearattending_id,
                            CASE WHEN COUNT(pco.parent_yearattending_id) = 2 THEN
                                CASE WHEN MIN(cp.pronoun_id) = 1000 AND MAX(cp.pronoun_id) = 1001 THEN \"family\"
                                    WHEN MIN(cp.pronoun_id) = 1001 AND MAX(cp.pronoun_id) = 1001 THEN \"family-dress\"
                                    ELSE \"family-pants\" END
                            WHEN COUNT(pco.parent_yearattending_id) = 1 THEN IF(cp.pronoun_id = 1001, \"person-dress-child\", \"person-child\")
                            ELSE \"people-group\" END icon,
                            CONCAT(cp.firstname, ' ', cp.lastname) parentname
                        FROM parents__child_expo pco, yearsattending yap, campers cp
                        WHERE parent_yearattending_id = yap.id AND yap.camper_id = cp.id
                        GROUP BY pco.child_yearattending_id) pcod ON pcod.child_yearattending_id = ya.id
                    WHERE ya.camper_id = c.id AND c.pronoun_id = p.id AND c.family_id = myfamily_id;
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS expose_nametags_family');
    }
};
