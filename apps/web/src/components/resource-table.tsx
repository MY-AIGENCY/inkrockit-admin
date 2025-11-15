import { ReactNode } from 'react';

export interface TableColumn<T> {
  key: string;
  header: string;
  render: (row: T) => ReactNode;
}

interface Props<T> {
  columns: TableColumn<T>[];
  data: T[];
  emptyLabel: string;
}

export function ResourceTable<T>({ columns, data, emptyLabel }: Props<T>) {
  if (!data.length) {
    return <p className="rounded-lg border border-dashed border-slate-200 p-6 text-sm text-slate-500">{emptyLabel}</p>;
  }

  return (
    <div className="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
      <table className="min-w-full divide-y divide-slate-200">
        <thead className="bg-slate-50">
          <tr>
            {columns.map((column) => (
              <th
                key={column.key}
                scope="col"
                className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500"
              >
                {column.header}
              </th>
            ))}
          </tr>
        </thead>
        <tbody className="divide-y divide-slate-100 bg-white text-sm">
          {data.map((row, rowIndex) => (
            <tr key={rowIndex} className="hover:bg-slate-50">
              {columns.map((column) => (
                <td key={`${column.key}-${rowIndex}`} className="whitespace-nowrap px-4 py-3 text-slate-700">
                  {column.render(row)}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
