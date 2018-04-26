SELECT *
INTO OUTFILE '/var/lib/mysql/centaur_export.csv'
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n'
FROM centaur.responses;
