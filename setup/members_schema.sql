/*
 * Create the members table, this is the members table the contains all
 * registered voters prior to the election and should be created in the
 * $db_acc_name specified in election_auth.php
 */
CREATE TABLE members
(
    first_name  VARCHAR(32), 
    last_name   VARCHAR(32), 
    username    VARCHAR(32), 
    password    BLOB, 
    email       VARCHAR(64), 
    join_date   DATE, 
    access_account SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, 
    PRIMARY KEY (access_account)
);