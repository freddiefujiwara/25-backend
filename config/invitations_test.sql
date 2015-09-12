CREATE TABLE invitations_test (
  user_id      char(016),
  hash         char(016),
  invited_from char(016),
  created_at   timestamp,
  clicked_at   timestamp,
  paired_at    timestamp
);
