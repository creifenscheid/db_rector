## TYPO3 Extension "DB Rector"

## What does it do
This extension is an adapter to run typo3-rector in TYPO3 backend to update the typoscript and TSConfig stored in the database in fields:constants,config of table:sys_template and field:TSConfig of table:pages.

To do so, the content is stored in a temporary file, which is handed over to typo3-rector. 
The result is read into a db model, which can be viewed and applied in the backend module.

## Features
- backend module to
  - view all typoscript and tsconfig stored in the db
  - run typo3-rector on single db entries 
  - apply typo3-rector result to original db entries
  - view difference between original and processed typoscript and tsconfig
- security: backend module is only active in TYPO3 context „development“ - can be deactivated via extension configuration


## Installation

Install this extension via `composer req creifenscheid/db-rector` or download it from the [TYPO3 Extension Repository](https://extensions.typo3.org/extension/db_rector/) and activate
the extension in the Extension Manager of your TYPO3 installation.

### Support
I don't want your money or anything else.
I am doing this for fun, with heart and to improve my coding skills.
Constructive critisism is very welcome.
If you want to contribute, feel free to do so.
Thank you!
