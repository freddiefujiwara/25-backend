CREATE TABLE invitations (
  user_id      char(016)     primary key,
  hash         char(016),
  invited_from char(016),
  updated_at   timestamp
);
