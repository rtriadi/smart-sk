# Requirements Document

## Introduction

This feature modifies the SK Editor to automatically use the default position (pejabat) from the master position settings instead of requiring users to manually select a position from a dropdown. This streamlines the document creation process by eliminating an unnecessary selection step when a default position is already configured.

## Glossary

- **SK Editor**: The Smart Editor interface for creating official documents (Surat Keputusan)
- **Pejabat**: Position/official role (e.g., Ketua, Wakil Ketua, Panitera, Sekretaris)
- **Master Position Settings**: The administrative interface where positions are configured and default positions are set
- **Default Position**: The position marked as `is_default = 1` in the master position settings
- **Position Selection Dropdown**: The current UI element that allows users to select a position manually
- **Auto-Population**: The process of automatically filling position-related fields without user intervention

## Requirements

### Requirement 1

**User Story:** As a user creating an SK document, I want the system to automatically use the default position from master settings, so that I don't need to manually select a position every time.

#### Acceptance Criteria

1. WHEN a user opens the SK Editor for creating a new document, THE SK Editor SHALL automatically populate position-related fields with the default position from master settings
2. WHEN no default position is configured in master settings, THE SK Editor SHALL display an informative message indicating that a default position needs to be set
3. WHEN the default position is successfully loaded, THE SK Editor SHALL populate the nama_penandatangan, jabatan_penandatangan, and nip_penandatangan fields automatically
4. WHEN a user opens an existing draft, THE SK Editor SHALL preserve the previously selected position data without overriding it with the default
5. THE SK Editor SHALL remove the position selection dropdown from the user interface

### Requirement 2

**User Story:** As an administrator, I want to ensure that the default position setting in master configuration is properly utilized, so that the system maintains consistency across all documents.

#### Acceptance Criteria

1. WHEN a default position is set in master position settings, THE system SHALL use this position for all new SK documents
2. WHEN the default position data is updated in master settings, THE system SHALL reflect these changes in new documents created after the update
3. WHEN multiple positions exist but no default is set, THE system SHALL provide clear guidance to administrators about setting a default position
4. THE system SHALL maintain backward compatibility with existing drafts that have position data already saved

### Requirement 3

**User Story:** As a user, I want clear feedback about the position being used in my document, so that I can verify the correct signatory information is applied.

#### Acceptance Criteria

1. WHEN the default position is automatically loaded, THE SK Editor SHALL display the position information in a read-only format with clear labeling
2. WHEN position fields are auto-populated, THE SK Editor SHALL show an indicator that these fields are automatically managed from master settings
3. THE SK Editor SHALL display the full position title, name, and NIP in the appropriate fields
4. WHEN there is an error loading the default position, THE SK Editor SHALL display a clear error message with instructions for resolution