CREATE TABLE IF NOT EXISTS RateLimit(
	id INTEGER PRIMARY KEY AUTOINCREMENT,
	ip TEXT NOT NULL,
	requested_at INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_ratelimit_ip_timestamp ON RateLimit(ip, requested_at);