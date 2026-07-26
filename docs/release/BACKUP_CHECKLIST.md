# Backup Checklist

- [ ] `VELORA_BACKUP_DISK` configured
- [ ] `VELORA_BACKUP_RETENTION_DAYS` set (default 14)
- [ ] Daily database backup scheduled via `velora:scheduler-run`
- [ ] Backup storage path writable
- [ ] Restore procedure documented and tested
- [ ] Off-site backup replication configured
- [ ] Backup run logs reviewed in admin Operations center
