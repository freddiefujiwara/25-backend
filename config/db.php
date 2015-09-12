<?php
if(getenv('DATABASE_URL')){ // production
    putenv('TABLE_NAME=invitations');
}else{ // test env
    putenv('DATABASE_URL='.'postgres://upgfcliaqbblkv:qLKTpEe2qER_eYVjt8ac7RQl9d@ec2-107-21-105-116.compute-1.amazonaws.com:5432/d535jun11lbse1');
    putenv('TABLE_NAME=invitations_test');
}
