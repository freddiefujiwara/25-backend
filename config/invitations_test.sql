CREATE TABLE invitations_test (
  user_id      char(016),
  hash         char(016),
  invited_to   char(016),
  issued_at   timestamp,
  clicked_at   timestamp,
  invited_at   timestamp
);
