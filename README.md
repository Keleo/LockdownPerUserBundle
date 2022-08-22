# LockdownPerUser Bundle

A Kimai plugin that allows to set the lockdown period per user.

## Installation

This plugin is compatible with the following Kimai releases:

| Bundle version     | Minimum Kimai version    |
|--------------------|--------------------------|
| 1.0                | 1.16.9                   |

### Copy files

Extract the ZIP file and upload the included directory and all files to your Kimai installation to the new directory:  
`var/plugins/LockdownPerUserBundle/`

The file structure needs to be like this afterwards:

```bash
var/plugins/
├── LockdownPerUserBundle
│   ├── LockdownPerUserBundle.php
|   └ ... more files and directories follow here ... 
```

### Clear cache

After uploading the files, Kimai needs to know about the new plugin. It will be found, once the cache was re-built:

```bash
cd kimai/
bin/console kimai:reload --env=prod
```

## Permissions

This bundle comes with the following permissions:

- `lockdown_per_user` - allow to configure the user specific lockdown settings

By default, it is assigned to each user with the role `ROLE_SUPER_ADMIN`.

Read how to manage the permission in the [documentation](https://www.kimai.org/documentation/permissions.html).
