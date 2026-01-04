-- Run this if you already have an existing DB from previous version:
ALTER TABLE events ADD COLUMN end_time DATETIME NULL AFTER start_time;
CREATE INDEX idx_events_end_time ON events(end_time);
