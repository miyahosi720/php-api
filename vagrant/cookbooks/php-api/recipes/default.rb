execute "drop db" do
  command "echo 'drop database if exists apidb' | mysql -uroot"
end

execute "create db" do
  command "echo 'create database apidb' | mysql -uroot"
end

execute "import db" do
  command "mysql -uroot apidb</var/www/html/data/apidb_dump.sql"
end
