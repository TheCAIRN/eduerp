# eduerp
A more simplistic ERP system than is usually found in industry, for purposes of teaching systems and systems design.

## Background
Our requirements for setting up an ERP system for a simulated company are,
1. The system must be operationally complete, from purchasing and production to sales and invoicing;
2. The system must be open source;
3. The system must install on MySQL (and variants) and/or MS SQL Server.
Though not a requirement, a browser based interface would make the system more accessible to non-technical 
business students.

We found iCompiere to be too complex for our needs, and the PostgreSQL requirement was beyond the scope of
our curriculum.  Inoerp looked good, and we tried it for a year; but the setup ended up being a bit buggy, and
students struggled with using the system.  The documentation is great for people who have been in industry for
a number of years, but lacks the simplicity for someone just starting out.

After careful consideration, we have decided that the best option is to create our own system.

