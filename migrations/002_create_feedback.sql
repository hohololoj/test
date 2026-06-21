CREATE TABLE IF NOT EXISTS Feedback(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT NOT NULL,
	email TEXT NOT NULL,
	phone TEXT NOT NULL,
    comment TEXT NOT NULL,
	sentiment TEXT DEFAULT NULL,
    type TEXT DEFAULT NULL,
	created_at INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_feedback_created_at ON Feedback(created_at);