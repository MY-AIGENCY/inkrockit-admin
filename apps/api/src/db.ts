import { createPool } from 'mysql2/promise';

const pool = createPool({
  host: process.env.MYSQL_HOST!,
  user: process.env.MYSQL_USER!,
  password: process.env.MYSQL_PASSWORD!,
  database: process.env.MYSQL_DATABASE!,
  port: Number(process.env.MYSQL_PORT ?? 3306),
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  timezone: 'Z',
});

export async function query<T = any>(sql: string, params: any[] = []) {
  const [rows] = await pool.execute(sql, params);
  return rows as T;
}

export { pool };
