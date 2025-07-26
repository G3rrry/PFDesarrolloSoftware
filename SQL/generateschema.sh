PGPASSWORD=root pg_dump -U root ds --schema-only > schema.sql ; PGPASSWORD=root pg_dump -U root ds --data-only --column-inserts >> schema.sql
