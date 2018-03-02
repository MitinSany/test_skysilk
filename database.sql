PRAGMA foreign_keys = OFF;

-- ----------------------------
-- Table structure for "main"."sqlite_sequence"
-- ----------------------------
DROP TABLE sqlite_sequence;
CREATE TABLE sqlite_sequence(name,seq);

-- ----------------------------
-- Records of sqlite_sequence
-- ----------------------------
INSERT INTO "main"."sqlite_sequence" VALUES ('users', null);

-- ----------------------------
-- Table structure for "users"
-- ----------------------------
DROP TABLE users;
CREATE TABLE users (
  "id"  INTEGER PRIMARY KEY AUTOINCREMENT,
  "email"  TEXT NOT NULL,
  "password"  TEXT NOT NULL,
  "firstName"  TEXT,
  "lastName"  TEXT,
  "signupCode"  TEXT,
  "activated"  INTEGER DEFAULT 0,
  CONSTRAINT "uniq_email" UNIQUE ("email" COLLATE BINARY ASC),
  CONSTRAINT "uniq_signup_code" UNIQUE ("signupCode" COLLATE BINARY ASC)
);

