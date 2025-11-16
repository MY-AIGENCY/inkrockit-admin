import express from 'express';
import cors from 'cors';
import morgan from 'morgan';
import router from './routes/index.js';
import dotenv from 'dotenv';

dotenv.config({ path: process.env.NODE_ENV === 'production' ? '.env' : '../../.env' });

const app = express();
app.use(cors());
app.use(express.json());
app.use(morgan('dev'));
app.use(router);

const port = Number(process.env.PORT || 4000);

app.listen(port, () => {
  console.log(`API server listening on port ${port}`);
});
