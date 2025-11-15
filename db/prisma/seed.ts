import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

async function main() {
  await prisma.savedFilter.create({
    data: {
      view: 'prospects',
      name: 'West Coast Active',
      createdBy: 'system',
      filters: { regions: ['West'] },
      isDefault: true,
    },
  });
}

main()
  .catch((error) => {
    console.error(error);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
