#!/usr/bin/env python3
"""
Generate a customer-focused data dictionary for the preprod MySQL database.

The script inspects a curated list of customer-related tables, detects which
columns actually contain data, and renders a Markdown report with table/column
names, SQL data types, nullability, and whether any non-null values exist.

Run this script on the server where MySQL is accessible via the local socket:

    python3 scripts/generate_customer_data_dictionary.py \
        --output reports/customer_data_dictionary.md
"""

from __future__ import annotations

import argparse
import textwrap
from dataclasses import dataclass
from pathlib import Path
from typing import Dict, Iterable, List, Sequence, Tuple

import mysql.connector
from mysql.connector.connection import MySQLConnection


DEFAULT_SOCKET = "/var/run/mysqld/mysqld.sock"
DEFAULT_DB = "preprod"
DEFAULT_USER = "preprod_user"
DEFAULT_PASSWORD = "!1q2w3eZ"
DEFAULT_OUTPUT = Path("reports/customer_data_dictionary.md")

# Tables that regularly store customer-facing data. Extend as needed.
CUSTOMER_TABLES: Sequence[str] = [
    "users",
    "users_company",
    "users_company_exec",
    "user_additional_info",
    "users_tmp_data",
    "user_jobs",
    "requests",
    "requests_more_sent",
    "request_notes",
    "request_note_required",
    "request_samples",
    "request_samples_collection",
    "eye_user_company",
    "credit_card",
    "credit_card_billing",
    "credit_card_shipping",
    "cc_shipping_jobs",
    "payment_history",
    "message_inbox",
    "order_changes",
    "events",
    "estimates",
    "estimate_items",
    "estimate_options",
]

TEXT_TYPES = {
    "char",
    "varchar",
    "text",
    "tinytext",
    "mediumtext",
    "longtext",
}


@dataclass
class ColumnInfo:
    name: str
    data_type: str
    column_type: str
    is_nullable: str
    has_data: bool
    table: str


def get_connection() -> MySQLConnection:
    return mysql.connector.connect(
        user=DEFAULT_USER,
        password=DEFAULT_PASSWORD,
        unix_socket=DEFAULT_SOCKET,
        database=DEFAULT_DB,
        charset="utf8",
    )


def fetch_columns(conn: MySQLConnection, table: str) -> List[ColumnInfo]:
    cur = conn.cursor(dictionary=True)
    cur.execute(
        """
        SELECT column_name, data_type, column_type, is_nullable
        FROM information_schema.columns
        WHERE table_schema = %s AND table_name = %s
        ORDER BY ordinal_position
        """,
        (DEFAULT_DB, table),
    )
    columns: List[ColumnInfo] = []
    for row in cur:
        def get(key: str) -> str:
            return row.get(key) or row.get(key.upper())

        columns.append(
            ColumnInfo(
                name=get("column_name"),
                data_type=get("data_type"),
                column_type=get("column_type"),
                is_nullable=get("is_nullable"),
                has_data=False,
                table=table,
            )
        )
    cur.close()
    return columns


def column_has_data(
    conn: MySQLConnection, table: str, column: str, data_type: str
) :  # -> bool
    cur = conn.cursor()
    if data_type.lower() in TEXT_TYPES:
        query = (
            f"SELECT 1 FROM `{table}` "
            f"WHERE `{column}` IS NOT NULL AND TRIM(`{column}`) <> '' LIMIT 1"
        )
    else:
        query = f"SELECT 1 FROM `{table}` WHERE `{column}` IS NOT NULL LIMIT 1"
    cur.execute(query)
    has_row = cur.fetchone() is not None
    cur.close()
    return has_row


def build_dictionary(conn: MySQLConnection) -> Dict[str, List[ColumnInfo]]:
    data: Dict[str, List[ColumnInfo]] = {}
    for table in CUSTOMER_TABLES:
        columns = fetch_columns(conn, table)
        populated: List[ColumnInfo] = []
        for col in columns:
            col.has_data = column_has_data(conn, table, col.name, col.data_type)
            if col.has_data:
                populated.append(col)
        if populated:
            data[table] = populated
    return data


def render_markdown(dictionary: Dict[str, List[ColumnInfo]]) -> str:
    lines: List[str] = [
        "# Customer Data Dictionary",
        "",
        "Only columns that currently contain at least one non-null (and, for text "
        "fields, non-empty) value are listed.",
        "",
    ]
    for table, columns in sorted(dictionary.items()):
        lines.append(f"## `{table}`")
        lines.append("")
        lines.append("| Column | SQL Type | Nullable | Notes |")
        lines.append("| --- | --- | --- | --- |")
        for col in columns:
            notes = ""
            lines.append(
                f"| `{col.name}` | `{col.column_type}` | {col.is_nullable} | {notes} |"
            )
        lines.append("")
    return "\n".join(lines)


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Generate a customer data dictionary.")
    parser.add_argument(
        "--output",
        type=Path,
        default=DEFAULT_OUTPUT,
        help=f"Output Markdown path (default: {DEFAULT_OUTPUT})",
    )
    return parser.parse_args()


def main() -> None:
    args = parse_args()
    conn = get_connection()
    try:
        dictionary = build_dictionary(conn)
    finally:
        conn.close()

    output = render_markdown(dictionary)
    args.output.parent.mkdir(parents=True, exist_ok=True)
    args.output.write_text(output, encoding="utf-8")
    print(f"Wrote data dictionary for {len(dictionary)} tables to {args.output}")


if __name__ == "__main__":
    main()

